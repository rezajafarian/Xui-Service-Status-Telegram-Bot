<?php

error_reporting(false);

include_once 'lib/jdf.php';
date_default_timezone_set('Asia/Tehran');

$time = date('H:i:s');
$date = jdate('Y/m/d');

$bot = [

    'token' => '6264764025:AAGups2hSzW8lYcD9IGcTxc194Vvb-w9Q1I',
    'admin' => 5068240372,
    'domin' => str_replace('/' . explode('/', $_SERVER['SCRIPT_NAME'])[2], null, $_SERVER['SCRIPT_URI']),
    'username' => '@XuiInfoBot',
    
    'database' => [
        'db_name' => 'bottelegrammrbot_info',
        'db_username' => 'bottelegrammrbot_info',
        'db_password' => 'reza1385reza',
    ],
   
];

$connect = mysqli_connect('localhost', $bot['database']['db_username'], $bot['database']['db_password'], $bot['database']['db_name']);
if ($connect->connect_error) {
    die("Connection failed: " . $sql->connect_error);
}

define('API_KEY', $bot['token']);
define('ADMIN', $bot['admin']);

# ----------------- [ <- variables -> ] ----------------- #

$update = json_decode(file_get_contents('php://input'));

$from_id = $message_id = $text = $username = $first_name = null;
if (isset($update->message)) {
    $message = $update->message;
    $from_id = $message->from->id;
    $message_id = $message->message_id;
    $text = $message->text;
    $username = $message->from->username ?? '';
    $first_name = $message->from->first_name ?? '';
} elseif (isset($update->callback_query)) {
    $query = $update->callback_query;
    $from_id = $query->from->id;
    $data = $query->data;
    $query_id = $query->id;
    $message = $query->message;
    $message_id = $message->message_id;
}


# ----------------- [ <- functions -> ] ----------------- #

function bot($method, $datas = []) {
    $url = "https://api.telegram.org/bot" . API_KEY . "/" . $method;
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => $datas
    ]);
    $res = curl_exec($ch);
    if ($res === false) {
        error_log('cURL Error: ' . curl_error($ch));
    } else {
        return json_decode($res);
    }
    curl_close($ch);
}


function sendMessage($chat_id, $text, $keyboard = null, $mrk = 'html') {
    $params = [
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => $mrk,
        'disable_web_page_preview' => true,
        'reply_markup' => $keyboard
    ];
    return bot('sendMessage', $params);
}

function forwardMessage($from, $to, $message_id, $mrk = 'html') {
    $params = [
        'chat_id' => $to,
        'from_chat_id' => $from,
        'message_id' => $message_id,
        'parse_mode' => $mrk
    ];
    return bot('forwardMessage', $params);
}

function editMessage($chat_id, $text, $message_id, $keyboard = null, $mrk = 'html') {
    $params = [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => $text,
        'parse_mode' => $mrk,
        'disable_web_page_preview' => true,
        'reply_markup' => $keyboard
    ];
    return bot('editMessageText', $params);
}

function login($serverIp, $serverName, $serverPass, $cookie = 'cookie.txt'){
    
    if(strpos($serverIp, 'http') === false){
    	echo 'your link is invalid';
        exit();
    }
    
    $loginUrl = $serverIp . '/login';
        
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $loginUrl);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('username' => $serverName, 'password' => $serverPass)));
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
    $loginResponse = curl_exec($ch);
    
    return $loginResponse;
    
}

function step($step){
    
    global $connect, $from_id;
    $connect->query("UPDATE `user` SET `step` = '$step' WHERE `from_id` = '$from_id'");
    
}

function request($url, $method = false, array $headers = null, $data = null){
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => $method,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HEADER => false,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true,
    ]);
    
    $result = json_decode(curl_exec($ch), true);
    curl_close($ch);
    
    return $result;
    
}

function ForConversion(int $byte, string $one = 'MB') {
    switch ($one) {
        case 'GB':
            $limit = floor($byte / 1073741824);
            break;
        case 'MB':
        default:
            $limit = floor($byte / 1048576);
            break;
    }
    return $limit;
}

# ----------------- [ <- keyboard -> ] ----------------- #

$panel = json_encode(['keyboard' => [
    
    [['text' => '👤 آمار کلی ربات']],
    [['text' => '➕ افزودن پنل'], ['text' => '✏️ مدیریت پنل ها']],
    [['text' => '📫 فوروارد همگانی'], ['text' => '📫 ارسال همگانی']],
    [['text' => '🔙 بازگشت به صفحه اصلی']],
    
], 'resize_keyboard' => true]);

if($from_id == $bot['admin']){
    
    $panel_key = json_encode(['keyboard' => [
        
        [['text' => '👮‍♂️ پنل مدیریت']],
        
    ], 'resize_keyboard' => true]);
    
}

$back = json_encode(['keyboard' => [
    
    [['text' => '⬅️ برگشت']],
    
], 'resize_keyboard' => true]);

$back_panel = json_encode(['keyboard' => [
    
    [['text' => '👈🏻⁩ بازگشت به پنل']],
    
], 'resize_keyboard' => true]);

# ----------------- [ <- others -> ] ----------------- #

$user_sql = $connect->query("SELECT `step` FROM `user` WHERE `from_id` = '$from_id' LIMIT 1");
if ($user_sql) {
  if ($user_sql->num_rows > 0) {
    $user = $user_sql->fetch_assoc();
    $step = $user['step'];
  } else {
    $connect->query("INSERT INTO `user`(`from_id`, `step`) VALUES ('$from_id', 'none')");
    $step = 'none';
  }
} else {
  // Handle database connection error
  die("Database connection error: " . $connect->connect_error);
}












