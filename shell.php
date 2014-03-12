<?php
#DB data
$db_hostname = 'localhost';
$db_database = 'stovot_trentondb';
$db_username = 'stovot_trenton';
$db_password = 'Trenton[F5i]';
$mysqli_err = false;
#Current timestamp
date_default_timezone_set("GMT");
$timeStamp = date("Y-m-d H:i:s");

#log into DB

$db_server = new mysqli($db_hostname, $db_username, $db_password, $db_database);
if ($db_server->connect_error) {
    logger("Couldn't connect to DB: " . $db_server->connect_error);
    $mysqli_err = $db_server->connect_error;
}

#Get a post var that is escaped
function getEscapedGET($var)
{
    return htmlspecialchars($_GET[$var]);
}

function getEscapedPOST($var)
{
    return htmlspecialchars($_POST[$var]);
}

#log some txt
function logger($txt)
{
    global $timeStamp;
    file_put_contents("log.txt", "$timeStamp " . $txt . "\n", FILE_APPEND | LOCK_EX);
}

?>