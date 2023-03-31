<?php

include_once '../config.php';

$chek = $connect->query("SELECT * FROM `send`")->fetch_assoc();

if (isset($chek['step']) && isset($chek['send']) && $chek['send'] === 'yes') {
    $allUserCount = $connect->query("SELECT COUNT(*) as count FROM `user`")->fetch_assoc()['count'];
    $usersQuery = $connect->query("SELECT * FROM `user`");

    if ($chek['type'] === 'text') {
        while ($row = $usersQuery->fetch_assoc()) {
            sendmessage($row['from_id'], $chek['text'], $start_key, 'html');
        }
    } elseif ($chek['type'] === 'photo') {
        while ($row = $usersQuery->fetch_assoc()) {
            bot('sendphoto', [
                'chat_id' => $row['from_id'],
                'photo' => $chek['text'],
                'caption' => $chek['caption'],
                'parse_mode' => 'html'
            ]);
        }
    } elseif ($chek['type'] === 'forward') {
        while ($row = $usersQuery->fetch_assoc()) {
            bot('ForwardMessage', [
                'chat_id' => $row['from_id'],
                'from_chat_id' => $chek['text'],
                'message_id' => $chek['caption'],
                'parse_mode' => 'html'
            ]);
        }
    }

    $connect->query("UPDATE `send` SET `user` = 0, `send` = 'no', `type` = NULL, `text` = NULL, `step` = NULL");
    sendmessage(ADMIN, "✏️ پیام همگانی با موفقیت به $allUserCount نفر ارسال شد");
}

echo 'successful.';
