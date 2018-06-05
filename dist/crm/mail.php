<?php 


$message = "Регистрация со следующими данными: \n";
$message .= "ВАРИАНТ УЧАСТИЯ: {$data['course']} \n";
$message .= "Имя: {$data['name']} \n";
$message .= "Email: {$data['email']} \n";
$message .= "Телефон: {$data['phone']} \n";
$message .= "utm: {$data['utm']} \n";

if (!empty($data['salesProblems'])) {
	$message .= "С какими проблемами в бизнесе вы сталкиваетесь чаще всего?: {$data['salesProblems']} \n";
	$message .= "Что хотите улучшить в вашем бизнесе в первую очередь?: {$data['sphere']} \n";
	$message .= "Что хотите узнать на конференции?: {$data['whatKnow']} \n";
}

$to = "danil.yashta@gmail.com" . ",";  
$to .= "zungng7@gmail.com";
// $to = "litvin.andriy91@gmail.com";

$headers = "Content-type: text/plain;charset=utf-8"; 
$subject = "=?UTF-8?B?".base64_encode("РЕГИСТРАЦИЯ-")."?=";


$status = mail($to, $subject, $message); 