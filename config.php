<?php

$token = ''; # توکن ربات شما
$api = ''; # رمزینه دریافت شده از سایت one-api.ir

define('URL', $url);
define("API_KEY", $token);

const DATABASE_HOSTNAME = 'localhost'; # تغییر ندید
const DATABASE_USERNAME = ''; # نام کاربری دیتابیس
const DATABASE_PASSWORD = ''; # پسورد کاربری دیتابیبس
const DATABASE_NAME = ''; # نام دیتابیس

$db = new mysqli(
    hostname: DATABASE_HOSTNAME,
    username: DATABASE_USERNAME,
    password: DATABASE_PASSWORD,
    database: DATABASE_NAME
);
$db->set_charset('utf8mb4');