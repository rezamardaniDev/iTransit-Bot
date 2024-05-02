<?php

include ("config.php");

mysqli_multi_query(
    $db,
    "CREATE TABLE IF NOT EXISTS `users` (
        `id` int PRIMARY KEY AUTO_INCREMENT,
        `first_name` text,
        `username` text NULL,
        `chat_id` bigint,
        `step` text NULL,
        `action` text NULL DEFAULT 'microsoft',
        `lang` text NULL,
        `query` text NULL
)default charset = utf8mb4;"
);
if (mysqli_connect_errno()) {
    echo "<center>";
    echo "به دلیل مشکل زیر، اتصال برقرار نشد : <br />" . mysqli_connect_error();
    echo "</center>";
    die();
} else {
    echo "دیتابیس متصل و نصب شد .";
}