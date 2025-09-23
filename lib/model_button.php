<?php
namespace RubikaPhp\Models;
class ButtonSelectionItem {
    public string $text;
    public ?string $image_url;
    public string $type;

    public function __construct(
        string $text,
        ?string $image_url = null,
        \RubikaPhp\Enums\ButtonSelectionTypeEnum $type
    )
    {
        $this->text = $text;
        if (isset($image_url)) $this->image_url = $image_url;
        $this->type = $type->value;
    }
}
class ButtonSelection {
    public string $selection_id;
    public string $search_type;
    public string $get_type;
    public array $items;
    public bool $is_multi_selection;
    public string $columns_count;
    public string $title;

    public function __construct(
        string $selection_id,
        \RubikaPhp\Enums\ButtonSelectionSearchEnum $search_type,
        \RubikaPhp\Enums\ButtonSelectionGetEnum $get_type,
        array $items,
        bool $is_multi_selection,
        string $columns_count,
        string $title
    )
    {
        $this->selection_id = $selection_id;
        $this->search_type = $search_type->value;
        $this->get_type = $get_type->value;
        $this->items = $items;
        $this->is_multi_selection = $is_multi_selection;
        $this->columns_count = $columns_count;
        $this->title = $title;
    }
}