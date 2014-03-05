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
$logIntoDB = function () {
    global $db_password, $db_username, $db_database, $db_hostname, $mysqli_err;
    $conn = new mysqli($db_hostname, $db_username, $db_password, $db_database);
    if ($conn->connect_error) {
        logger("Couldn't connect to DB: " . $conn->connect_error);
        $mysqli_err = $conn->connect_error;
    }
    return $conn;
};

$db_server = $logIntoDB();

unset($logIntoDB);

#Get a post var that is escaped
function getEscapedPost($var)
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