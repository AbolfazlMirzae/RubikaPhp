# مستندات RubikaPhp
**RubikaPhp**

کتابخانه ای برای تعامل با **Rubika Bot Api** 
# دانلود و نصب
```bash
git clone https://github.com/AbolfazlMirzae/RubikaPhp.git
```
# Core
### Bot
---
**ساخت بات**
---
```php
require_once('rubikaphp/lib/autoload.php');

use RubikaPhp\Core\Bot;

$bot = new Bot('Your Bot Token');
```
**ارسال پیام**
---
```php
$chat_id = 'c12345'; #Your ChatId
$text = 'Hello From RubikaPhp'; #Your Text

$bot->chatId($chat_id)
    ->text($text)
    ->sendMessage();
```
**ارسال فایل**
---
```php
$chat_id = 'c123456';,²
$filePath = 'path/to/file.png'; #Optional

$bot->chatId($chat_id)
    ->filePath($filePath)
    ->sendFile();
```


