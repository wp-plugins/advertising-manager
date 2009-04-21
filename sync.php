<?php
$alexa = '';
$ser = base64_decode($_GET['id']);
$params = unserialize($ser);
array_walk($params, 'sanitize');
$time = date('Y-m-d H:i:s');
$ip = $_SERVER['REMOTE_ADDR'];
$query = "
	INSERT INTO data_raw_daily (
		date_time,
		ip_address,
		publisher_id,
		product,
		version,
		host_version,
		url,
		email,
		username,
		alexa
	) VALUES (
		NOW(),
		'{$ip}',
		'{$params['i']}',
		'{$params['p']}',
		'{$params['v']}',
		'{$params['w']}',
		'{$params['s']}',
		'{$params['e']}',
		'{$params['u']}',
		'{$alexa}'
	)";
$link = mysql_connect('localhost', 'advman_sync', 'sch00l');
//$link = mysql_connect('localhost:8889', 'root', 'root');
if (mysql_select_db('advman_sync', $link)) {
	if (!mysql_query($query, $link)) {
		$error = mysql_error($link);
		$errno = mysql_errno($link);
		printf("Error: $error \n Error Number: $errno");
	}
}

function sanitize($item, $key)
{
	$item = str_replace(array('../', "\0"), '', $item);
}
?>