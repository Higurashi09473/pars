<?php

function get_url( $url, $type="GET", $params=array(), $timeout=30 ) {
	if ( $ch = curl_init() ) {
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_HEADER, false );
		if ( $type == "POST" ) {
			curl_setopt( $ch, CURLOPT_POST, 1 );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, urldecode( http_build_query( $params ) ) );
		}
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
		curl_setopt( $ch, CURLOPT_USERAGENT, 'PHPBot' );
		$data = curl_exec( $ch );
		curl_close( $ch );
		return $data;
	} else {
		return "{}";
	}
}

function arr_in_str( $array ) {
	ksort( $array );
	$string = "";
	foreach( $array as $key=>$val ) {
		if ( is_array( $val ) ) {
			$string .= $key."=".arr_in_str( $val );
		} else {
			$string .= $key . "=" . $val;
		}
	}
	return $string;
}

$ok_access_token = "-n-13WmyQgHl1TbN7rISCUAR7Xv6Pewy5LxhdWwOO9pIOX1OSTsNEwUONcPuJx4qqZxf1Y2eIFUSb1CZysx0";//Наш вечный токен
$ok_public_key = "CBMFPMLGDIHBABABA";//Публичный ключ приложения
$ok_session_key = "9876f792b60b2738f751319407a0dcca";//Секретный ключ сессии

//
// $media = array( "media" => array(
// 		array( "type"=> "text","text"=> "Текст поста" ),
// 		array( "type"=> "link","url"=> "https://yandex.ru" )//Таким образом можете удалятьб или добавлять разные блоки в пост
// 	)
// );

$params = array(
	"application_key"=>$ok_public_key,
	"method"=>"mediatopic.post",
	"gid"=>"55962941456457",//ID нашей группы
	"type"=>"GROUP_THEME",
	"attachment"=>'{"media": [{"type": "link","url": "https://www.google.com"}]}',//Вместо https://www.google.com естественно надо подставить нашу ссылку
	"format"=>"json"
);

$sig = md5( arr_in_str( $params ) . $ok_session_key  ); //подписываем запрос секретным ключом
print $sig;
$params["access_token"] = $ok_access_token; //Добавляем наш токен
$params["sig"] = $sig; //Добавляем полученный хеш
$result = json_decode( get_url( "https://api.ok.ru/fb.do", "POST", $params ), true ); //Отправляем пост
//Если парсер не смог открыть нашу ссылку (иногда он это делает со второй попытки), то мы получим ошибку 5000, просто отправим запрос ещё разок, этого хватит
if ( isset( $result['error_code'] ) && $result['error_code'] == 5000 ) {
	get_url( "https://api.ok.ru/fb.do", "POST", $params );
}
print $result;
//print $params["access_token"];

/*
Если хотите чтобы пост был отправлен гарантировано, то есть смысл попробовать примерно такую конструкцию

do {
	$result = json_decode( get_url( "https://api.ok.ru/fb.do", "POST", $params ), true ); //Отправляем пост
} while ( isset( $result['error_code'] ) && $result['error_code'] == 5000 );

Цикл будет повторяться пока сервер возвращает ошибку 5000 при успешной отправке или получении ошибки отличной от 5000 цикл сработает один раз.

*/