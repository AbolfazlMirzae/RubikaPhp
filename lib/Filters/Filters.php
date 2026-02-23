<?php
namespace RubikaPhp\Filters;

use RubikaPhp\Enums\MessageSenderEnum;
use RubikaPhp\Models\Update;

class Filters {
    protected array $filters = [];

    public function __construct(callable $filter) {
        $this->filters[] = $filter;
    }

    public function check(Update $update): bool {
        foreach ($this->filters as $filter) {
            if (!$filter($update)) return false;
        }
        return true;
    }

    public function and(Filters $other): Filters {
        return new Filters(function(Update $update) use ($other) {
            return $this->check($update) && $other->check($update);
        });
    }

    public function or(Filters $other): Filters {
        return new Filters(function(Update $update) use ($other) {
            return $this->check($update) || $other->check($update);
        });
    }

    public static function where(string $str1, string $str2): Filters {
        return new Filters(fn() => $str1 == $str2);
    }
    
    public static function any(): Filters {
        return new Filters(fn(Update $update) => true);
    }

    public static function text(?string $expected = null): Filters {
        return new Filters(function(Update $update) use ($expected) {
            return $update->new_message?->text !== null &&
                ($expected === null || ($update->new_message?->text ?? $update->updated_message?->text) === $expected || str_starts_with(trim($update->new_message?->text ?? $update->updated_message?->text), $expected));
        });
    }

    public static function command(string $cmd): Filters {
        return new Filters(function(Update $update) use ($cmd) {
            return ($update->new_message?->text ?? $update->updated_message?->text) !== null &&
                str_starts_with(trim($update->new_message?->text ?? $update->updated_message?->text), "/$cmd");
        });
    }

    public static function isEdited(): Filters {
        return new Filters(fn(Update $update) => $update->new_message?->is_edited ?? $update->updated_message?->is_edited ?? false);
    }

    public static function isForwarded(): Filters {
        return new Filters(fn(Update $update) => ($update->new_message?->forwarded_from ?? $update->updated_message?->forwarded_from) !== null);
    }

    public static function hasContact(): Filters {
        return new Filters(fn(Update $update) => ($update->new_message?->contact_message ?? $update->updated_message?->contact_message) !== null);
    }

    public static function isReply(): Filters {
        return new Filters(fn(Update $update) => ($update->new_message?->reply_to_message_id ?? $update->updated_message?->reply_to_message_id) !== null);
    }

    public static function hasFile(): Filters {
        return new Filters(fn(Update $update) => ($update->new_message?->file ?? $update->updated_message?->file) !== null);
    }

    public static function hasSticker(): Filters {
        return new Filters(fn(Update $update) => ($update->new_message?->sticker ?? $update->updated_message?->sticker) !== null);
    }

    public static function hasLocation(): Filters {
        return new Filters(fn(Update $update) => ($update->new_message?->location ?? $update->updated_message?->location) !== null);
    }

    public static function hasPoll(): Filters {
        return new Filters(fn(Update $update) => ($update->new_message?->poll ?? $update->updated_message?->poll) !== null);
    }

    public static function hasLiveLocation(): Filters {
        return new Filters(fn(Update $update) => $update->new_message?->live_location !== null);
    }

    public static function removedMessage(): Filters {
        return new Filters(fn(Update $update) => $update->removed_message_id !== null);
    }

    public static function inlineUpdate(): Filters {
        return new Filters(fn(Update $update) => $update->inline_message !== null);
    }

    public static function updatedPayment(): Filters {
        return new Filters(fn(Update $update) => $update->updated_payment !== null);
    }

    public static function user(string $userId): Filters {
        return new Filters(fn(Update $update) => ($update->new_message?->sender_type ?? $update->updated_message?->sender_type) === $userId);
    }

    public static function isBot(): Filters {
        return new Filters(fn(Update $update) => $update->new_message?->sender_type === MessageSenderEnum::BOT);
    }

    public static function senderType(MessageSenderEnum $type): Filters {
        return new Filters(fn(Update $update) => $update->new_message?->sender_type === $type);
    }

    public static function contains(string $needle): Filters {
        return new Filters(fn(Update $update) => str_contains($update->new_message?->text ?? "", $needle));
    }

    public static function regex(string $pattern): Filters {
        return new Filters(fn(Update $update) => preg_match($pattern, $update->new_message?->text ?? "") === 1);
    }

    public static function length(int $min, ?int $max = null): Filters {
        return new Filters(fn(Update $update) => 
            ($len = strlen($update->new_message?->text ?? "")) >= $min &&
            ($max === null || $len <= $max)
        );
    }

    public static function custom(callable $func): Filters {
        return new Filters($func);
    }
}