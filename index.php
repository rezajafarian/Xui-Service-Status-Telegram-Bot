<?php

include_once 'config.php';
include_once 'other/rezajafarian.php';

if($text == '/start' or $text == '⬅️ برگشت' or $text == '🔙 بازگشت به صفحه اصلی'){

    step('information');
    sendmessage($from_id, "👋 - سلام [ <b>$first_name</b> ] عزیز به ربات ما خوش آمدید.\n\nℹ️ - برای دیدن اطلاعات اشتراکتون لطفا لینک اشتراکتون رو ارسال کنید !\n\n⏱ - <code>$date - $time</code>", $panel_key);
    
}

if ($step == 'information' && $text != '/start' && $text != '/panel' && $text != '👮‍♂️ پنل مدیریت') {
    
    $protocols = [
        'vless' => 'vless://',
        'vmess' => 'vmess://'
    ];

    $protocol = null;
    foreach ($protocols as $key => $value) {
        if (strpos($text, $value) !== false) {
            $protocol = $key;
            break;
        }
    }

    if ($protocol === null) {
        sendmessage($from_id, "❌ اشتراک ارسالی شما اشتباه است.");
        exit();
    }

    sendmessage($from_id, "🌐 در حال دریافت اطلاعات . . .");

    $info = null;
    if ($protocol === 'vless') {
        $info = [
            'name' => explode('#', $text)[1] ?? null,
            'domain' => explode(':', explode('@', $text)[1])[0] ?? null
        ];
    } elseif ($protocol === 'vmess') {
        $info = json_decode(base64_decode(str_replace('vmess://', '', $text)), true);
        $info = [
            'name' => $info['ps'] ?? null,
            'domain' => $info['add'] ?? null
        ];
    }

    if ($info['name'] === null || $info['domain'] === null) {
        sendmessage($from_id, "❌ اشتراک ارسالی شما معتبر نیست.");
        exit();
    }

    $url = "{$bot['domin']}/lib/web.php?domin={$info['domain']}&step=status&name={$info['name']}&protocol=$protocol";
    $get = json_decode(file_get_contents($url), true);

    if (isset($get['ok']) && !$get['results']) {
        sendmessage($from_id, "❌ اشتراک ارسالی شما یافت نشد.");
        exit();
    }

    $results = $get['results'];
    $enable = $results['enable'];
    $up = ForConversion($results['up'], 'MB');
    $down = ForConversion($results['down'], 'MB');
    $time = $results['expiryTime'];
    $total = ForConversion($results['total'], 'GB');

    $time = $time == 0 ? '∞' : date('Y-d-m', $time / 1000);
    $total = $total == 0 ? '∞' : $total . ' GB';
    $remaining = $up + $down == 0 ? '∞' : $up + $down;

    if ($up > 999) {
        $up = ($up / 1000) . ' GB';
    } else {
        $up .= ' MB';
    }

    if ($down > 999) {
        $down = ($down / 1000) . ' GB';
    } else {
        $down .= ' MB';
    }

    if ($remaining > 999) {
        $remaining = ($remaining / 1000) . ' GB';
    } else {
        $remaining .= ' MB';
    }
    
     $status = $enable ? '✅' : '❌';
    
    $txt = "🆔 - نام : <b>{$info['name']}</b>\n♻️ - وضعیت : <b>$status</b>\n⬆️ - آپلود↑ : <code>$up</code>\n⬇️ - دانلود↓ : <code>$down</code>\n⭕️ - حجم کل : <code>$remaining</code> / <code>$total</code>\n⏰ - تاریخ : <code>$time</code>";
    sendmessage($from_id, $txt, $start_key);
}

elseif($from_id == $bot['admin']){
    
    if($text == '/panel' or $text == '👮‍♂️ پنل مدیریت' or $text == '👈🏻⁩ بازگشت به پنل'){
        
        step('panel');
        sendmessage($from_id, "👋 به پنل مدیریت ربات خوش آمدید.", $panel);
            
    }
    
    if($text == '👤 آمار کلی ربات'){
        
        $users = mysqli_num_rows($connect->query("SELECT * FROM `user`")) ?? 0;
        sendmessage($from_id, "👤 آمار ربات شما : <code>$users</code> نفر");
        
    }
    
    elseif($text == '➕ افزودن پنل'){
    
    	step('add_panel');
    	$txt = "◽️ لینک لاگین پنل خود را ارسال کنید :";
    	sendmessage($from_id, $txt, $back);
    	
    }
    
    elseif($step == 'add_panel' and $text != '⬅️ برگشت'){
        
    	if(strpos($text, 'http') === false){
    		step('add_panel');
    		$txt = "❌ لینک ارسالی شما باید با http یا https باشد.";
    		sendmessage($from_id, $txt, $back);
    		exit();
    	}
    	
    	step('send_user');
    	file_put_contents('link.txt', str_replace([' ', "\n", "\t"], null, $text));
    	$txt = "◽️ یوزرنیم پنل خود را ارسال کنید :";
    	sendmessage($from_id, $txt, $back);
    	
    }
    elseif($step == 'send_user' and $text != '⬅️ برگشت'){
        
    	file_put_contents('username.txt', $text);
    	step('send_pass');
    	$txt = "◽️ پسورد پنل خود را ارسال کنید :";
    	sendmessage($from_id, $txt, $back);
    	
    }
    elseif($step == 'send_pass' and $text != '⬅️ برگشت'){
        
    	file_put_contents('password.txt', $text);
    	step('send_domin');
    	$txt = "◽️ دامین پنل را ارسال کنید :";
    	sendmessage($from_id, $txt, $back);
    	
    }
    elseif($step == 'send_domin' and $text != '⬅️ برگشت'){
    	
    	$server = file_get_contents('link.txt');
    	$username = file_get_contents('username.txt');
    	$password = file_get_contents('password.txt');
    	$res = login($server, $username, $password, false);
    	
    	if(json_decode($res)->success == 1){
    	    step('panel');
    	    
    	    $session = str_replace(["\t", "\n", " "], null, explode(' ', explode("session", file_get_contents('cookie.txt'))[1])[0]);
    	    $connect->query("INSERT INTO `panels` (`login_link`, `username`, `password`, `domin`, `session`) VALUES ('$server', '$username', '$password', '$text', '$session')");
    	    
    		$txt = "✅ ربات با موفقیت بر پنل شما لاگین کرد.";
    		sendmessage($from_id, $txt, $panel);
    	}elseif(json_decode($res)->success == 0){
            step('panel');
    		$txt = "⚠️ یوزرنیم یا پسورد اشتباه است.";
    		sendmessage($from_id, $txt, $panel);
        }else{
            step('panel');
    		$txt = "⚠️ خطای ناشناخته ای یافت شد.";
    		sendmessage($from_id, $txt, $panel);
        }
        
        $files = ['link.txt', 'username.txt', 'password.txt', 'error_log'];
        foreach($files as $name){
            if(is_file($name)) unlink($name);
        }
        
    }

    
    elseif($text == '✏️ مدیریت پنل ها' or $data == 'back_panellist'){
        
        $select = $connect->query("SELECT * FROM `panels`");
        if($select->num_rows == 0){
            sendmessage($from_id, "❌ لیست پنل های ربات خالی است !");
            exit();
        }
        
        while($row = $select->fetch_assoc()){
            $panels[] = [['text' => '🗑️', 'callback_data' => 'del-'.$row['row']], ['text' => $row['domin'], 'callback_data' => 'info-'.$row['row']]];    
        }
        
        if(!isset($data)){
            sendmessage($from_id, "✏️ لیست پنل های شما به شرح زیر است ، از طریق دکمه های زیر میتوانید آن ها را مدیریت کنید :↓", json_encode(['inline_keyboard' => $panels]));
        }else{
            editmessage($from_id, "✏️ لیست پنل های شما به شرح زیر است ، از طریق دکمه های زیر میتوانید آن ها را مدیریت کنید :↓", $message_id, json_encode(['inline_keyboard' => $panels]));
        }
    }
    
    if(isset($data)) {
        
        if(strpos($data, 'del-') !== false) {
            $id = explode('-', $data)[1];
            $connect->query("DELETE FROM `panels` WHERE `row` = '$id' LIMIT 1");
            $key = json_encode(['inline_keyboard' => [
                [['text' => '🔎 بازگشت به لیست پنل ها', 'callback_data' => 'back_panellist']],
            ]]);
            editmessage($from_id, "✅ پنل انتخابی شما با موفقیت حذف شد.", $message_id, $key);
        }
        
        elseif(strpos($data, 'info-') !== false) {
            $id = explode('-', $data)[1];
            $panel = $connect->query("SELECT `domin` FROM `panels` WHERE `row` = '$id' LIMIT 1")->fetch_assoc();
            bot('AnswerCallbackQuery', [
               'callback_query_id' => $query_id,
               'text' => $panel['domin'],
               'show_alert' => true
            ]);
        }
        
    }

    
    elseif($text == '📫 ارسال همگانی'){
        
        step('send_all');
        sendmessage($from_id, "👈🏻⁩ متن خود را ارسال کنید :", $back_panel);
        
    }
    
    elseif($step == 'send_all' and $text != '👈🏻⁩ بازگشت به پنل'){
        
        step('none');
        
        if (isset($update->message->text)){
            $type = 'text';
        }else{
            $type = $update->message->photo[count($update->message->photo)-1]->file_id;
            $text = $update->message->caption;
        }
        
        $connect->query("UPDATE `send` SET `send` = 'yes', `text` = '$text', `type` = '$type', `step` = 'send'");
        
        sendmessage($from_id, "✅ پیام شما با موفقیت به صف ارسال همگانی اضافه شد !", $panel);

    }

    elseif($text == '📫 فوروارد همگانی'){
        
        step('for_all');
        sendmessage($from_id, "👈🏻⁩ متن خود را فوروارد کنید :", $back_panel);
        
    }
    
    elseif($step == 'for_all' and $text != '👈🏻⁩ بازگشت به پنل'){
        
        step('none');
        $connect->query("UPDATE `send` SET `send` = 'yes', `text` = '$message_id', `type` = '$from_id', `step` = 'forward'");
        sendmessage($from_id, "✅ پیام شما با موفقیت به صف فوروارد همگانی اضافه شد !", $panel);
        
    }
    
    elseif($text == '/cancel_send'){
        
        step('panel');
        $connect->query("UPDATE `send` SET `send` = 'no', `text` = NULL, `type` = NULL, `step` = 'none'");
        sendmessage($from_id, "❌ با موفقیت لغو شد.", $panel);
        
    }
    
}

#-----------------------------#

?>