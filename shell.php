<?php

$_PAGE_DATA = "";

#DB data
$db_hostname = 'localhost';
$db_database = 'stovot_trentondb';
$db_username = 'stovot_trenton';
$db_password = 'Trenton[F5i]';

#Current timestamp
$timeStamp = date("Y-m-d H:i:s");

#log into DB
$logIntoDB = function () {
    global $db_password, $db_username, $db_database, $db_hostname;

    $db_server = mysql_connect($db_hostname, $db_username, $db_password);
    if ($db_server === false) {
        logger("Failed to connect to to MySQL: " . mysql_error());
        exit("unable to connect to database, please try again later.");
    }
    if (mysql_select_db($db_database, $db_server) === false) {
        logger("Failed to select a database: " . mysql_error());
        exit("unable to connect to database, please try again later.");
    }
    return $db_server;
};

$db_server = $logIntoDB();

unset($logIntoDB);

#Get a post var that is escaped
function getEscapedPost($var)
{
    return mysql_real_escape_string(htmlspecialchars($_POST[$var]));
}

#log some txt
function logger($txt)
{
    global $timeStamp;
    file_put_contents("log.txt", "$timeStamp " . $txt . "\n", FILE_APPEND | LOCK_EX);
}


function printToPage($pageData)
{
    ?>

    <html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Movies!</title>
        <script type="text/javascript" src="/bootstrap/js/jquery.js"></script>
        <link rel="stylesheet" type="text/css" href="/bootstrap/css/bootstrap-theme.min.css" media="screen"/>
        <link rel="stylesheet" type="text/css" href="/bootstrap/css/bootstrap.min.css" media="screen"/>
        <link rel="stylesheet" type="text/css" href="/bootstrap/js/plugins/flot/jquery.flot.js" media="screen"/>
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        <!--[if lte IE 8]>
        <script language="javascript" type="text/javascript" src="bootstrap/js/plugins/flot/excanvas.min.js"></script>
        <![endif]-->
        <style>
            table td {
                text-align: center;
                margin: auto;
            }

            table.data td {
                padding: 6px;
            }

            .error {
                color: red;
            }
        </style>
        <script>

            //The following code is under the MIT licensce and was taken from this post:
            //http://www.hagenburger.net/BLOG/HTML5-Input-Placeholder-Fix-With-jQuery.html
            $('[placeholder]').focus(function () {
                var input = $(this);
                if (input.val() == input.attr('placeholder')) {
                    input.val('');
                    input.removeClass('placeholder');
                }
            }).blur(function () {
                    var input = $(this);
                    if (input.val() == '' || input.val() == input.attr('placeholder')) {
                        input.addClass('placeholder');
                        input.val(input.attr('placeholder'));
                    }
                }).blur().parents('form').submit(function () {
                    $(this).find('[placeholder]').each(function () {
                        var input = $(this);
                        if (input.val() == input.attr('placeholder')) {
                            input.val('');
                        }
                    })
                });
            //End code from blog post
        </script>
    </head>
    <body>
    <div class="container-fluid text-center">

        <div class="header">
            <h1>Movies!</h1>
            <a href="index.php">Insert</a>
            <a href="search.php">Search</a>
            <a href="#" class="btn btn-danger">Login</a>
            <a href="./Admin/index.php" class="btn btn-primary">Admin</a>
        </div>

        <?php echo $pageData; ?>

    </div>
    </body>
    </html>
<?php } ?>