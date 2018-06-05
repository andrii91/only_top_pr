<?php 

function getVar($name)
{
    return isset($_POST[$name]) ? trim($_POST[$name]) : null;
}
function GetClearPhoneNumber($number) {
  if (empty($number)) {
    return "";
  }
  $number = str_replace('(', '', $number);
  $number = str_replace(')', '', $number);
  $number = str_replace('-', '', $number);
  // $number = str_replace('+', '', $number);
  $number = str_replace(' ', '', $number);
  return $number;
}

$data = [
	'name' => getVar('name'),
	'phone' => GetClearPhoneNumber(getVar('phone')),
	'email' => getVar('email'),
	'course' => getVar('course'),
	'utm' => getVar('utm'),
	'sphere' => getVar('sphere'),
	'company' => getVar('company'),
	'role' => getVar('role'),
	'numberManagers' => getVar('numberManagers'),
	'salesProblems' => getVar('salesProblems'),
	'source' => '709421',
	'whatKnow' => getVar('whatKnow')
];



require 'mail.php';

switch ($data['course']) {
	case 'Пакет «BUSINESS»':
		$data['product'] = "";
		break;
	
	case 'Пакет «V.I.P.»':
		$data['product'] = "";
		break;
	
	
	default:
		$data['product'] = "";
		break;
}


use \AmoCRM\Handler;
use \AmoCRM\Request;
use \AmoCRM\Lead;
use \AmoCRM\Contact;
use \AmoCRM\Note;
use \AmoCRM\Task;

require('vendor/autoload.php');




/* Оборачиваем в try{} catch(){}, чтобы отлавливать исключения */
try {
	$api = new Handler('onlytop', 'ril.onlytop@gmail.com');

 $request_get = $api->request(new Request(Request::GET, ['query' => $data['phone']], ['leads', 'list']));

	/* Создаем сделку,
	$api->config содержит в себе массив конфига,
	который вы создавали в начале */
	$lead = new Lead();
	$lead
		/* Название сделки */
		->setName('PR-СТРАТЕГИЯ ДЛЯ БИЗНЕСА') 
		/* Назначаем ответственного менеджера */
		->setResponsibleUserId($api->config['ResponsibleUserId'])
		/* Кастомное поле */
		->setCustomField(
			$api->config['utm'], // ID поля
			$data['utm'] // ID значения поля
		)
		->setCustomField(
			$api->config['sphere'], // ID поля 
			$data['sphere'] // ID значения поля
		)
		->setCustomField(
			$api->config['company'], // ID поля 
			$data['company'] // ID значения поля
		)
		
		->setCustomField(
			$api->config['role'], // ID поля 
			$data['role'] // ID значения поля
		)
		
		->setCustomField(
			$api->config['numberManagers'], // ID поля 
			$data['numberManagers'] // ID значения поля
		)
		
		->setCustomField(
			$api->config['salesProblems'], // ID поля 
			$data['salesProblems'] // ID значения поля
		)
		
		->setCustomField(
			$api->config['whatKnow'], // ID поля 
			$data['whatKnow'] // ID значения поля
		)
		
		->setCustomField(
			$api->config['source'], // ID поля 
			$data['source'] // ID значения поля
		)
		
		->setCustomField(
			$api->config['product'], // ID поля 
			$data['product'] // ID значения поля
		)
		
		/* Теги. Строка - если один тег, массив - если несколько */
		->setTags(['#'.$data['course']])
		/* Статус сделки */
		->setStatusId($api->config['LeadStatusId']);


			$api->request(new Request(Request::SET, $lead));



	/* Сохраняем ID новой сделки для использования в дальнейшем */
	$lead = $api->last_insert_id;


	/* Создаем контакт */
	$contact = new Contact();
	$contact
		/* Имя */
		->setName($data['name'])
		/* Назначаем ответственного менеджера */
		->setResponsibleUserId($api->config['ResponsibleUserId'])
		/* Привязка созданной сделки к контакту */
		->setLinkedLeadsId($lead)
		/* Кастомные поля */
		->setCustomField(
			$api->config['ContactFieldPhone'],
			$data['phone'], // Номер телефона
			'MOB' // MOB - это ENUM для этого поля, список доступных значений смотрите в информации об аккаунте
		) 
		->setCustomField(
			$api->config['ContactFieldEmail'],
			$data['email'], // Email
			'WORK' // WORK - это ENUM для этого поля, список доступных значений смотрите в информации об аккаунте
		) 
		/* Теги. Строка - если один тег, массив - если несколько */
		->setTags(['Заявка', $data["course"]]);

	/* Проверяем по емейлу, есть ли пользователь в нашей базе */
	$api->request(new Request(Request::GET, ['query' => $data['email']], ['contacts', 'list']));
/*
	echo "<pre>";
	print_r($api);*/

	/* Если пользователя нет, вернется false, если есть - объект пользователя */
	$contact_exists = ($api->result) ? $api->result->contacts[0] : false;

	/* Если такой пользователь уже есть - мержим поля */
	if ($contact_exists) {
		$contact
			/* Указываем, что пользователь будет обновлен */
			->setUpdate($contact_exists->id, $contact_exists->last_modified + 1)
			/* Ответственного менеджера оставляем кто был */
			->setResponsibleUserId($contact_exists->responsible_user_id)
			/* Старые привязанные сделки тоже сохраняем */
			->setLinkedLeadsId($contact_exists->linked_leads_id);
	}



	/* Отправляем все в AmoCRM */
	$api->request(new Request(Request::SET, $contact));
	// $api->request(new Request(Request::SET, $note));
	// $api->request(new Request(Request::SET, $task));
	


} catch (\Exception $e) {
	echo $e->getMessage();
}

// header('Location: /smm2018-thx/');
?>