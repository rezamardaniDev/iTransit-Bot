<?php

ini_set("display_errors", 'on');
ini_set('log_errors', 'on');
ini_set('display_startup_errors', 'on');
ini_set('error_reporting', E_ALL);

require 'config.php';

$update = json_decode(file_get_contents('php://input'));

########## handler ########## 
function bot(string $method, array $params)
{
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://api.telegram.org/bot' . API_KEY . '/' . $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $params
    ]);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;

}

########## variable ##########
if (isset($update->message)) {
    $message = $update->message;
    $chat_id = $update->message->from->id;
    $username = $update->message->chat->username;
    $first_name = $update->message->chat->first_name;
    $from_id = $update->message->from->id;
    $text = $update->message->text;
    $msg_id = $update->message->message_id;
}
if (isset($update->callback_query)) {
    $call_user_id = $update->callback_query->from->id;
    $data = $update->callback_query->data;
    $callback_id = $update->callback_query->message->message_id;
}


########## functions ##########
function translateRequestApi($token, $action, $lang, $query)
{
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://one-api.ir/translate/?token=$token&action=$action&lang=$lang&q=$query",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET'
    ]);
    $response = curl_exec($curl);
    curl_close($curl);
    return (json_decode($response)->result);
}
;
function setStep($db, $chat_id, $step)
{
    $sql = "UPDATE `users` SET `step` = '$step' WHERE `chat_id` = $chat_id ";
    mysqli_query($db, $sql);
}
function getUser($db, $chat_id)
{
    $sql = "SELECT * FROM `users` WHERE `chat_id`= $chat_id ";
    $result = mysqli_fetch_assoc(mysqli_query($db, $sql));

    return $result;
}


########## main ##########
if ($text == '/start' or $text == 'منو اصلی') {
    $sql = "SELECT `chat_id` FROM `users` WHERE `chat_id`=$chat_id";
    $result = mysqli_query($db, $sql);

    $res = mysqli_fetch_assoc($result);
    if (!$res) {
        $sql2 = "INSERT INTO `users` (`first_name`, `username`, `chat_id`) VALUES ('$first_name', '$username', $chat_id)";
        $result2 = mysqli_query($db, $sql2);
        bot('sendMessage', [
            'chat_id' => $chat_id,
            'text' => "لطفا یکی از گزینه های زیر را انتخاب کنید",
            'reply_markup' => json_encode([
                'resize_keyboard' => true,
                'keyboard' => [
                    [['text' => '💬 ترجمه متن']],
                    [['text' => '👤‌ پروفایل'], ['text' => '👨🏻‍💻 سورس کد']],
                    [['text' => '🌐 تغییر موتور جستجو']]
                ]
            ])
        ]);
        die();
    }
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => "لطفا یکی از گزینه های زیر را انتخاب کنید",
        'reply_markup' => json_encode([
            'resize_keyboard' => true,
            'keyboard' => [
                [['text' => '💬 ترجمه متن']],
                [['text' => '👤‌ پروفایل'], ['text' => '👨🏻‍💻 سورس کد']],
                [['text' => '🌐 تغییر موتور جستجو']]
            ]
        ])
    ]);
    setStep($db, $chat_id, 'Null');
}


if ($text == '💬 ترجمه متن') {
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => 'زبان مبدا و مقصد را انتخاب کنید:',
        'reply_markup' => json_encode([
            'resize_keyboard' => true,
            'keyboard' => [
                [['text' => '🇮🇷 فارسی به 🇺🇸 انگلیسی'], ['text' => '🇺🇸 انگلیسی به 🇮🇷 فارسی']],
                [['text' => 'منو اصلی']]
            ]
        ])
    ]);
    setStep($db, $chat_id, 'lang');
    die();
}


$sql = "SELECT * FROM `users` WHERE `chat_id`= $chat_id ";
$result = mysqli_fetch_assoc(mysqli_query($db, $sql));

if ($text == 'google' and $result['step'] == 'change_action') {
    $sql = "UPDATE `users` SET `action` = 'google' WHERE `chat_id` = $chat_id ";
    mysqli_query($db, $sql);

    $user = getUser($db, $chat_id);
    $action = $user['action'];
    $msg = "
موتور جستجوی شما تغییر کرد!

🤖 موتور جستجوی انتخاب شده:
$action

⛳️ برای تغییر میتوانید یکی از موتور های زیر را انتخاب کنید
    ";

    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => $msg,
        'reply_markup' => json_encode([
            'resize_keyboard' => true,
            'keyboard' => [
                [['text' => '💬 ترجمه متن']],
                [['text' => '👤‌ پروفایل'], ['text' => '👨🏻‍💻 سورس کد']],
                [['text' => '🌐 تغییر موتور جستجو']]
            ]
        ])
    ]);
}


if ($text == 'microsoft' and $result['step'] == 'change_action') {
    $sql = "UPDATE `users` SET `action` = 'microsoft' WHERE `chat_id` = $chat_id ";
    mysqli_query($db, $sql);

    $user = getUser($db, $chat_id);
    $action = $user['action'];
    $msg = "
موتور جستجوی شما تغییر کرد!

🤖 موتور جستجوی انتخاب شده:
$action

⛳️ برای تغییر میتوانید یکی از موتور های زیر را انتخاب کنید
    ";

    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => $msg,
        'reply_markup' => json_encode([
            'resize_keyboard' => true,
            'keyboard' => [
                [['text' => '💬 ترجمه متن']],
                [['text' => '👤‌ پروفایل'], ['text' => '👨🏻‍💻 سورس کد']],
                [['text' => '🌐 تغییر موتور جستجو']]
            ]
        ])
    ]);
}


if ($text == '🇮🇷 فارسی به 🇺🇸 انگلیسی' and $result['step'] == 'lang') {
    $sql = "UPDATE `users` SET `lang` = 'en' WHERE `chat_id` = $chat_id ";
    mysqli_query($db, $sql);

    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => 'کلمه یا جمله مورد نظرت رو برای ترجمه بفرست (حداکثر 2 خط) :',
        'reply_markup' => json_encode([
            'resize_keyboard' => true,
            'keyboard' => [
                [['text' => 'منو اصلی']]
            ]
        ])
    ]);
    setStep($db, $chat_id, 'word');
    die();
}


if ($text == '🇺🇸 انگلیسی به 🇮🇷 فارسی' and $result['step'] == 'lang') {
    $sql = "UPDATE `users` SET `lang` = 'fa' WHERE `chat_id` = $chat_id ";
    mysqli_query($db, $sql);

    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => 'کلمه یا جمله مورد نظرت رو برای ترجمه بفرست (حداکثر 2 خط) :',
        'reply_markup' => json_encode([
            'resize_keyboard' => true,
            'keyboard' => [
                [['text' => 'منو اصلی']]
            ]
        ])
    ]);
    setStep($db, $chat_id, 'word');
    die();
}


if ($result['step'] == 'word') {
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => 'درحال ترجمه لطفا منتظر باشید ...'
    ]);

    $text = str_replace(' ', '+', $text);
    $sql = "UPDATE `users` SET `query` = '$text' WHERE `chat_id` = $chat_id ";
    mysqli_query($db, $sql);

    $user = getUser($db, $chat_id);

    $action = $user['action'];
    $lang = $user['lang'];
    $query = $user['query'];


    $trans = translateRequestApi($api, $action, $lang, $query);
    bot('deleteMessage', [
        'chat_id' => $chat_id,
        'message_id' => $msg_id + 1
    ]);

    $msg = "
ترجمه شما آماده است!

$trans
    ";
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => $msg,
        'reply_markup' => json_encode([
            'resize_keyboard' => true,
            'keyboard' => [
                [['text' => '💬 ترجمه متن']],
                [['text' => '👤‌ پروفایل'], ['text' => '👨🏻‍💻 سورس کد']],
                [['text' => '🌐 تغییر موتور جستجو']]
            ]
        ])
    ]);
    setStep($db, $chat_id, 'Null');
    die();
}


if ($text == '👤‌ پروفایل') {
    $user = getUser($db, $chat_id);

    $action = $user["action"];
    $query = str_replace("+", " ", $user["query"]);

    $msg = "
🔸 اطلاعات حساب کاربری شما

👤 نام : $first_name
💎 شناسه عددی : $chat_id

☑️ موتور جستجوی پیشفرض : $action

🟢 آخرین ترجمه انجام شده شما برای متن زیر بوده :

$query
    ";

    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => $msg
    ]);
}


if ($text == '🌐 تغییر موتور جستجو') {
    setStep($db, $chat_id, 'change_action');

    $user = getUser($db, $chat_id);
    $action = $user['action'];
    $msg = "
🤖 موتور جستجوی فعلی شما:
$action

⛳️ برای تغییر میتوانید یکی از موتور های زیر را انتخاب کنید
    ";
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => $msg,
        'reply_markup' => json_encode([
            'resize_keyboard' => true,
            'keyboard' => [
                [['text' => 'google'], ['text' => 'microsoft']],
                [['text' => 'منو اصلی']]
            ]
        ])
    ]);
}
if ($text == '👨🏻‍💻 سورس کد') {
    $msg = "
🛠 سورس کد ربات بصورت آزاد بر روی آدرس زیر در دسترس است

https://github.com/rezamardaniDev/iTransit-Bot
    ";
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => $msg
    ]);
}