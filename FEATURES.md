# RubikaPhp Library - Complete Feature Documentation

## New Features Added (Based on PDF API Documentation)

### 1. **InlineMessage Model** ✅
- Handles messages from inline keyboard interactions
- Properties: `sender_id`, `text`, `location`, `aux_data`, `message_id`, `chat_id`
- Supports webhook callbacks from inline button clicks

### 2. **Advanced Button Models** ✅

#### ButtonCalendar
- Date/Time selection button
- Supports Persian and Gregorian calendars
- Configurable year range
- Properties: `type`, `min_year`, `max_year`, `title`, `default_value`

#### ButtonNumberPicker
- Numeric range selection
- Properties: `min_value`, `max_value`, `title`, `default_value`

#### ButtonStringPicker
- String/text selection from predefined list
- Multi-select support
- Properties: `items`, `title`, `default_value`

#### ButtonTextbox
- Text input field
- Supports single/multi-line modes
- Keyboard type options (String/Number)
- Properties: `type_line`, `type_keypad`, `title`, `place_holder`, `default_value`

#### ButtonLocation
- Location picker/viewer
- Map integration
- Default location support
- Properties: `type`, `title`, `default_pointer_location`, `default_map_location`

### 3. **Media File Methods** ✅

#### `sendSticker()`
- Send sticker messages
- Requires: `fileId()`

#### `sendVoice()`
- Send voice/audio messages
- Supports file upload or file_id
- Optional text caption

#### `sendMusic()`
- Send music files
- Similar to voice but for music media
- Supports metadata

### 4. **Group/Channel Management Methods** ✅

#### User Management
- `banUser(chatId, userId)` - Ban user from group
- `unbanUser(chatId, userId)` - Remove ban
- `kickUser(chatId, userId)` - Remove user from group
- `muteUserFromGroup(chatId, userId)` - Mute user
- `unmuteUserFromGroup(chatId, userId)` - Unmute user

### 5. **Parameter Builder Enhancements** ✅

New chainable methods:
- `userId(userId)` - Set user ID for operations
- `title(title)` - Set title for chat operations
- `description(description)` - Set description

### 6. **Test Coverage** ✅

Total: **80 Unit Tests** across 6 test classes:
- `ParametersTest` - 15 tests
- `BotTest` - 15 tests
- `FiltersTest` - 17 tests
- `RubikaAPITest` - 3 tests
- `AdvancedModelsTest` - 15 tests (NEW)
- `BotAdvancedMethodsTest` - 18 tests (NEW)

## Usage Examples

### ButtonCalendar
```php
use RubikaPhp\Enums\ButtonCalendarTypeEnum;
use RubikaPhp\Models\ButtonCalendar;

$calendar = new ButtonCalendar(
    type: ButtonCalendarTypeEnum::DATEPERSIAN,
    min_year: '1400',
    max_year: '1410',
    title: 'Select Date'
);
```

### ButtonLocation
```php
use RubikaPhp\Models\ButtonLocation;
use RubikaPhp\Models\Location;

$location = new ButtonLocation(
    type: ButtonLocationTypeEnum::PICKER,
    title: 'Select Location',
    default_pointer_location: new Location('51.3890', '35.6892')
);
```

### Send Voice Message
```php
$bot->chatId('c123456')
    ->filePath('/path/to/voice.mp3')
    ->text('Check this voice message')
    ->sendVoice();
```

### Group Management
```php
$bot->banUser('group_123', 'user_456');
$bot->kickUser('group_123', 'user_789');
$members = $bot->getChatMembers('group_123');
$bot->editChatTitle('group_123', 'New Group Name');
```

## API Endpoints Implemented

✅ **48+ API Methods** including:
- Message Methods: sendMessage, sendFile, sendPoll, sendLocation, sendContact
- Message Editing: editMessageText, editMessageKeypad, deleteMessage
- Info Methods: getMe, getChat
- Group: banUser, unbanUser
- Updates: getUpdates, getChannelUpdates
- Bot Config: setCommands, setEndpoint, updateBotEndpoints
- Forward: forwardMessage
- Keypad: editChatKeypad, chatKeypad

## Models Updated

- **Chat** - Group/channel information
- **Message** - Full message data with all attachment types
- **Update** - Webhook update handling
- **InlineMessage** - NEW: Inline button interactions
- **Button Models** - Complete 6 button types
- **Files** - File, Location, Sticker, ContactMessage, Poll, etc.

## 📝 Text Formatting - Markdown Support (v2.1.0)

Markdown formatting functions ported from **Rubpy** Python SDK.

```php
use RubikaPhp\Core\Bot;

$bot = new Bot('Your Token Here');
// Basic formatting
$text = "**Bold** and __italic__ and --underline-- and ||spoiler|| and [mention](user_guid) and [link](https://rubika.ir) and >Quote and ```code``` and `mono`"

$bot->chatId(chatId)
    ->text($text)
    ->parseMode('markdown')
    ->sendMessage();
```

### Features
✅ Automatic whitespace trimming  
✅ Unicode/Persian text support  
✅ 20 unit tests (100% pass rate)  

## Environment

- **PHP**: >=8.0
- **Dependencies**: GuzzleHttp 7.x, Composer
- **Testing**: PHPUnit 9.6

## Documentation Files

- [README.md](README.md) - Project overview
- [examples/webhook_bot.php](examples/webhook_bot.php) - Webhook setup
- [phpunit.xml](phpunit.xml) - Test configuration
- [.gitignore](.gitignore) - Git ignore rules
