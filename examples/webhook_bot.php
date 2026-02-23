<?php
/**
 * Webhook Example
 * This example shows how to use RubikaPhp with a webhook
 */

require_once(__DIR__ . '/../lib/autoload.php');

use RubikaPhp\Core\Bot;
use RubikaPhp\Filters\Filters;

// Initialize the bot
$bot = new Bot('YOUR_BOT_TOKEN_HERE');

// Setup webhook (run this once to configure)
// $bot->setEndpoint('https://yourdomain.com/webhook.php');

// Define handlers
$bot->onUpdate(Filters::text(), function($bot, $update) {
    $bot->chatId($update->new_message->chat_id)
        ->text('Echo: ' . $update->new_message->text)
        ->sendMessage();
});

$bot->onUpdate(Filters::command('start'), function($bot, $update) {
    $bot->chatId($update->new_message->chat_id)
        ->text('Bot is running via webhook!')
        ->sendMessage();
});

// Process webhook request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bot->run();
} else {
    echo "Webhook endpoint is ready.";
}
