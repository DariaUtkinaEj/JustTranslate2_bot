<?php

require_once __DIR__ . '/vendor/autoload.php';

use Telegram\Bot\Api;
use \Dejurin\GoogleTranslateForFree;

$token = '5663802282:AAEzkH0p3nOqpHh1i1QoJ-_HijO38F7cWvc';

$telegram = new Api($token);

$update = $telegram->getWebhookUpdates();

//file_put_contents(__DIR__ . '/logs.txt', print_r($update, 1), FILE_APPEND);

$chat_id = $update['message']['chat']['id'] ?? '';
$text = $update['message']['text'] ?? '';

if ($text == '/start') {
    $response = $telegram->sendMessage([
        'chat_id' => $chat_id,
        'text' => "Привет! Я бот-переводчик и помогу вам перевести с английского на русский и обратно. Просто отправьте мне слово или фразу.",
    ]);
} elseif (!empty($text)) {
    if (preg_match('#[a-z]+#i', $text)) {
        $source = 'en';
        $target = 'ru';
    } else {
        $source = 'ru';
        $target = 'en';
    }
    $attempts = 5;

    $tr = new GoogleTranslateForFree();
    $result = $tr->translate($source, $target, $text, $attempts);

    if ($result) {
        $response = $telegram->sendMessage([
            'chat_id' => $chat_id,
            'text' => $result,
        ]);
    } else {
        $response = $telegram->sendMessage([
            'chat_id' => $chat_id,
            'text' => 'Упс... я не смог перевести это...',
        ]);
    }
} else {
    $response = $telegram->sendMessage([
        'chat_id' => $chat_id,
        'text' => 'Это бот-переводчик, поэтому он ожидает от вас текст для перевода...',
    ]);
}