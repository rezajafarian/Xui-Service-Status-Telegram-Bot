<?php

include_once 'config.php';

$server = (string) $_POST['ssl'].'://'.$_POST['ip'].':'.$_POST['port'];
$loginResponse = login($server, $_POST['username'], $_POST['password']);

if(json_decode($loginResponse)->success == 1){
    
    $session = str_replace(["\t", "\n", " "], null, explode(' ', explode("session", file_get_contents('cookie.txt'))[1])[0]);
    $connect->query("INSERT INTO `panels` (`login_link`, `username`, `password`, `domin`, `session`) VALUES ('$server', '{$_POST['username']}', '{$_POST['password']}', '{$_POST['domain']}', '$session')");
    header('Location: '.$bot['domin'].'/html/success_message.html');
    
}else{
    
    header('Location: '.$bot['domin'].'/html/error_message.html');
    
}
