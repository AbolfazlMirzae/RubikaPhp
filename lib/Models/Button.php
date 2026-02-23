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

class ButtonCalendar {
    public ?string $default_value;
    public \RubikaPhp\Enums\ButtonCalendarTypeEnum $type;
    public string $min_year;
    public string $max_year;
    public string $title;

    public function __construct(
        \RubikaPhp\Enums\ButtonCalendarTypeEnum $type,
        string $min_year,
        string $max_year,
        string $title,
        ?string $default_value = null
    ) {
        $this->type = $type;
        $this->min_year = $min_year;
        $this->max_year = $max_year;
        $this->title = $title;
        $this->default_value = $default_value;
    }
}

class ButtonNumberPicker {
    public string $min_value;
    public string $max_value;
    public ?string $default_value;
    public string $title;

    public function __construct(
        string $min_value,
        string $max_value,
        string $title,
        ?string $default_value = null
    ) {
        $this->min_value = $min_value;
        $this->max_value = $max_value;
        $this->title = $title;
        $this->default_value = $default_value;
    }
}

class ButtonStringPicker {
    public array $items;
    public ?string $default_value;
    public ?string $title;

    public function __construct(
        array $items,
        ?string $title = null,
        ?string $default_value = null
    ) {
        $this->items = $items;
        $this->title = $title;
        $this->default_value = $default_value;
    }
}

class ButtonTextbox {
    public \RubikaPhp\Enums\ButtonTextboxTypeLineEnum $type_line;
    public \RubikaPhp\Enums\ButtonTextboxTypeKeypadEnum $type_keypad;
    public ?string $place_holder;
    public ?string $title;
    public ?string $default_value;

    public function __construct(
        \RubikaPhp\Enums\ButtonTextboxTypeLineEnum $type_line,
        \RubikaPhp\Enums\ButtonTextboxTypeKeypadEnum $type_keypad,
        ?string $title = null,
        ?string $place_holder = null,
        ?string $default_value = null
    ) {
        $this->type_line = $type_line;
        $this->type_keypad = $type_keypad;
        $this->title = $title;
        $this->place_holder = $place_holder;
        $this->default_value = $default_value;
    }
}

class ButtonLocation {
    public ?Location $default_pointer_location;
    public ?Location $default_map_location;
    public \RubikaPhp\Enums\ButtonLocationTypeEnum $type;
    public ?string $title;

    public function __construct(
        \RubikaPhp\Enums\ButtonLocationTypeEnum $type,
        ?string $title = null,
        ?Location $default_pointer_location = null,
        ?Location $default_map_location = null
    ) {
        $this->type = $type;
        $this->title = $title;
        $this->default_pointer_location = $default_pointer_location;
        $this->default_map_location = $default_map_location;
    }
}
