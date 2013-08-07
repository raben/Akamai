--TEST--
Akamai NetStorage Auth Signature Test
[
 * AuthData
 * AuthSign
]
--FILE--
<?php
require dirname(__FILE__).DIRECTORY_SEPARATOR.'../Akamai.php';

$config = parse_ini_file(dirname(__FILE__).DIRECTORY_SEPARATOR.'akamai.ini');
$auth = new Akamai_Netstorage_Authorize($config["key"], $config["key_name"], $config["version"]);
$auth->time = '1375770303';
$auth->unique_id = '3014002966';

$auth_data	= $auth->getAuthData();
$auth_sign	= $auth->getAuthSign($config["base_url"], 'version=1&action=dir&format=xml');

echo $auth_data."\n";
echo $auth_sign;
?>
--EXPECT--
5, 0.0.0.0, 0.0.0.0, 1375770303, 3014002966, apihdn
HXDTIxkLJrYw3XgJa+5smyJFLEUEVc8ZIztUxTMHbRU=
