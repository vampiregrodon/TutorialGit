<?php

// Put your private key's passphrase here:
$passphrase = 'pushchat';

// Put your alert message here:
$message = 'My first push notification!';
$ctx = stream_context_create();
	stream_context_set_option($ctx, 'ssl', 'local_cert', 'ck.pem');
	stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
	stream_context_set_option($ctx, 'ssl', 'cafile', 'aps_development.cer');

	// Open a connection to the APNS server
	$fp = stream_socket_client(
		'ssl://gateway.sandbox.push.apple.com:443', $err,
		$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

	if (!$fp)
		exit("Failed to connect: $err $errstr" . PHP_EOL);

	echo 'Connected to APNS' . PHP_EOL;


$sessionToken = new Memcache;
$sessionToken->connect('10.30.81.244', 11230);

/////////////////////////////////////////////////
$os = "ios";
$name_increment = "incerement_ios";
$script_access_count = $sessionToken->get($name_increment);

for ($i=0; $i <= $script_access_count; $i++) { 
	$generate_key_increment = $os."_" . $i; 
	// echo $generate_key_increment."<br>";
	echo $getValue = $sessionToken->get($generate_key_increment);
	pushNotifycation($message,$getValue,$fp);
}


// Close the connection to the server
fclose($fp);

/////////////////////////////////////////////////
function pushNotifycation($message,$deviceToken,$fp){
	$body['aps'] = array(
		'alert' => $message,
		'sound' => 'default'
		);

	// Encode the payload as JSON
	$payload = json_encode($body);

	// Build the binary notification
	
		$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

	// Send it to the server
		$result = fwrite($fp, $msg, strlen($msg));
	
	if (!$result)
		echo 'Message not delivered<br>\n' . PHP_EOL;
	else
		echo 'Message successfully delivered<br>\n' . PHP_EOL;

	
}




?>