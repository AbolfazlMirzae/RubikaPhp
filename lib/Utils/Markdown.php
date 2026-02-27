<?php

namespace RubikaPhp\Utils;

class Markdown
{
    private $MARKDOWN_RE = '/(?:^(?:> ?[^\n]*\n?)+)|```([\s\S]*?)```|\*\*([^\n*]+?)\*\*|`([^\n`]+?)`|__([^\n_]+?)__|--([^\n-]+?)--|~~([^\n~]+?)~~|\|\|([^\n|]+?)\|\||\[([^\]]+?)\]\((\S+)\)/m';
    
    private $MARKDOWN_TYPES = [
        ">" => ["Quote", null],
        "```" => ["Pre", 1],
        "**" => ["Bold", 2],
        "`" => ["Mono", 3],
        "__" => ["Italic", 4],
        "--" => ["Underline", 5],
        "~~" => ["Strike", 6],
        "||" => ["Spoiler", 7],
        "[" => ["Link", 8],
    ];

    private $MENTION_PREFIX_TYPES = [
        "u" => "User",
        "g" => "Group",
        "c" => "Channel",
        "b" => "Bot"
    ];

    public function htmlToMarkdown(string $html): string
    {
        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8">' . $html);
        libxml_clear_errors();
        $body = $doc->getElementsByTagName('body')->item(0);
        return $this->nodeToMarkdown($body);
    }

    private function nodeToMarkdown($node): string
    {
        $markdown = '';

        foreach ($node->childNodes as $child) {
            if ($child->nodeType === XML_TEXT_NODE) {
                $markdown .= $child->nodeValue;
            } elseif ($child->nodeType === XML_ELEMENT_NODE) {
                $tag = strtolower($child->nodeName);
                $inner = $this->nodeToMarkdown($child);

                switch ($tag) {
                    case 'b':
                    case 'strong':
                        $markdown .= "**{$inner}**";
                        break;
                    case 'i':
                    case 'em':
                        $markdown .= "__{$inner}__";
                        break;
                    case 'u':
                        $markdown .= "--{$inner}--";
                        break;
                    case 's':
                    case 'strike':
                        $markdown .= "~~{$inner}~~";
                        break;
                    case 'code':
                        $markdown .= "`{$inner}`";
                        break;
                    case 'pre':
                        $markdown .= "```\n{$inner}\n```";
                        break;
                    case 'blockquote':
                        $lines = explode("\n", $inner);
                        foreach ($lines as &$line) {
                            $line = "> " . $line;
                        }
                        $markdown .= implode("\n", $lines);
                        break;
                    case 'a':
                        $href = $child->getAttribute('href');
                        $markdown .= "[{$inner}]({$href})";
                        break;
                    case 'span':
                    case 'div':
                    default:
                        $markdown .= $inner;
                        break;
                }
            }
        }

        return $markdown;
    }

    private function buildUtf16PrefixLengths(string $text): array
    {
        $prefix_lengths = [0];
        $total = 0;
        for ($i = 0; $i < mb_strlen($text, 'UTF-8'); $i++) {
            $char = mb_substr($text, $i, 1, 'UTF-8');
            $ord = $this->uniOrd($char);
            $total += ($ord > 0xFFFF) ? 2 : 1;
            $prefix_lengths[] = $total;
        }
        return $prefix_lengths;
    }

    private function uniOrd($c) {
        $ord0 = ord($c[0]);
        if ($ord0 >= 0 && $ord0 <= 127) return $ord0;
        if ($ord0 >= 192 && $ord0 <= 223) return ($ord0-192)*64 + (ord($c[1])-128);
        if ($ord0 >= 224 && $ord0 <= 239) return ($ord0-224)*4096 + (ord($c[1])-128)*64 + (ord($c[2])-128);
        if ($ord0 >= 240 && $ord0 <= 247) return ($ord0-240)*262144 + (ord($c[1])-128)*4096 + (ord($c[2])-128)*64 + (ord($c[3])-128);
        return null;
    }
    
    public function toMetadata(string $text): array
    {
        $meta_data_parts = [];
        $current_text = $text;
        $offset = 0;
        $char_offset = 0;
        $utf16_prefix = $this->buildUtf16PrefixLengths($text);

        if (preg_match_all($this->MARKDOWN_RE, $text, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $index => $match) {
                $group = $match[0];
                $start = $match[1];
                $end = $start + strlen($group);

                // Convert byte offsets (from preg_offset_capture) to character indexes
                $char_start = mb_strlen(substr($text, 0, $start), 'UTF-8');
                $char_end = mb_strlen(substr($text, 0, $end), 'UTF-8');

                // Guard indexes exist in prefix array
                $utf16_start = $utf16_prefix[$char_start] ?? 0;
                $utf16_end = $utf16_prefix[$char_end] ?? $utf16_start;

                $adjusted_start = $utf16_start - $offset;
                $adjusted_char_start = $char_start - $char_offset;

                foreach ($this->MARKDOWN_TYPES as $prefix => $type_info) {
                    list($md_type, $group_idx) = $type_info;
                    if (strpos($group, $prefix) === 0) {

                        if ($md_type === "Quote") {
                            $lines = explode("\n", $group);
                            $content_lines = [];
                            foreach ($lines as $line) {
                                $line = rtrim($line, "\r\n");
                                if (strpos($line, '> ') === 0) {
                                    $content_lines[] = substr($line, 2);
                                } elseif (strpos($line, '>') === 0) {
                                    $content_lines[] = substr($line, 1);
                                } else {
                                    $content_lines[] = $line;
                                }
                            }
                            $content = implode("\n", $content_lines);
                        } else {
                            $content = isset($matches[$group_idx][$index][0]) ? $matches[$group_idx][$index][0] : '';
                        }

                        $content_length = strlen(mb_convert_encoding($content, 'UTF-16BE', 'UTF-8')) / 2;
                        $char_content_length = mb_strlen($content, 'UTF-8');

                        if (!in_array($md_type, ['Pre', 'Link'])) {
                            $inner = $this->toMetadata($content);
                            $content = $inner['text'];
                            $content_length = strlen(mb_convert_encoding($content, 'UTF-16BE', 'UTF-8')) / 2;
                            $char_content_length = mb_strlen($content, 'UTF-8');

                            if (isset($inner['metadata'])) {
                                foreach ($inner['metadata']['meta_data_parts'] as $part) {
                                    $part['from_index'] += $adjusted_start;
                                    $meta_data_parts[] = $part;
                                }
                            }
                        }

                        $meta_data_part = ["type" => $md_type, "from_index" => $adjusted_start, "length" => $content_length];

                        if ($md_type === "Pre") {
                            $lines = explode("\n", $content, 2);
                            $meta_data_part['language'] = trim($lines[0] ?? '');
                        } elseif ($md_type === "Link") {
                            $url = isset($matches[9][$index][0]) ? $matches[9][$index][0] : '';
                            $firstChar = mb_substr($url, 0, 1, 'UTF-8');
                            $mention_type = $this->MENTION_PREFIX_TYPES[$firstChar] ?? 'hyperlink';

                            if ($mention_type === 'hyperlink') {
                                $meta_data_part['link_url'] = $url;
                                $meta_data_part['link'] = ['type' => $mention_type, 'hyperlink_data' => ['url' => $url]];
                            } else {
                                $meta_data_part['type'] = "MentionText";
                                $meta_data_part['mention_text_object_guid'] = $url;
                                $meta_data_part['mention_text_user_id'] = $url;
                                $meta_data_part['mention_text_object_type'] = $mention_type;
                            }
                        }

                        $meta_data_parts[] = $meta_data_part;

                        $markup_length = $utf16_end - $utf16_start;
                        $char_markup_length = $char_end - $char_start;

                        $current_text = mb_substr($current_text, 0, $adjusted_char_start, 'UTF-8')
                            . $content
                            . mb_substr($current_text, $adjusted_char_start + $char_markup_length, null, 'UTF-8');

                        $offset += $markup_length - $content_length;
                        $char_offset += $char_markup_length - $char_content_length;

                        break;
                    }
                }
            }
        }

        $result = ["text" => trim($current_text)];
        if (!empty($meta_data_parts)) {
            $result['metadata'] = ['meta_data_parts' => $meta_data_parts];
        }

        return $result;
    }
}