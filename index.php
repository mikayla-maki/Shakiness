<?php
include_once("shell.php");
$sender = $_POST["sender"];

$error = "";
$title = "";
$director = "";
$shakiness = "";


if (isset($_POST["title"]) && isset($_POST["director"]) && isset($_POST["shakiness"])) {
    $title = getEscapedPOST("title");
    $director = getEscapedPOST("director");
    $shakiness = getEscapedPOST("shakiness");
    if (!is_numeric($shakiness)) {
        $error = "Please enter a number for 'shakiness'";
        $shakiness = "";
    } else {
        if ($stmt = $db_server->prepare("INSERT INTO movies(title,director,shakiness) VALUES (?,?,?)")) {
            $stmt->bind_param("ssi", $title, $director, $shakiness);
            $stmt->execute();
            $response = '
    <br />
    Thanks for the movie! Here\'s the data you submitted:
    <br />
    <br />
    <div class"table-responsive">
    <table class="data table">
        <tr>
            <td><b>Title</b></td>
            <td><b>Director</b></td>
            <td><b>Shakiness</b></td>
        </tr>
        <tr>
            <td>' . $title . '</td>
            <td>' . $director . '</td>
            <td>' . $shakiness . '</td>
        </tr>
    </table>
';
        } else {
            logger("Failed to insert: " . $db_server->errno);
            $error = "failed to submit to the database, please try again later";
        }
    }
}

if ($sender != "curl") {
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
        <script src="/bootstrap/js/bootstrap.min.js"></script>
        <script src="/bootstrap/js/plugins/flot/jquery.flot.js"></script>
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        <!--[if lte IE 8]>
        <script language="javascript" type="text/javascript" src="bootstrap/js/plugins/flot/excanvas.min.js"></script>
        <![endif]-->
        <script language="javascript" type="text/javascript" src="/js/core.js"></script>
        <link rel="stylesheet" type="text/css" href="css/core.css" media="screen"/>
    </head>
    <body>

    <div class="jumbotron">

        <h1>Hello Movie goers! <a class="btn btn-primary btn-xs pull-right" href="Admin/login.html">Log in</a></h1>

        <p>This site is intended to help you track various pieces of data about different times in movies. Is there a
            spot of shakiness that could make some people sick? How about a gory scene? Maybe some nudity you would
            rather avoid? You can find exactly when you should look away and even a short description of the plot in
            those sections with this service. Currently we are in the pre-alpha stage and what you see are some very
            simple proof of concepts and learning exercises. Eventually something more will be added</p>
    </div>

    <div class="container text-center">

        <ul class="nav nav-pills">
            <li class="active"><a href="index.php">Insert Movie</a></li>
            <li><a href="search.php">Search</a></li>
            <li><a href="Admin/index.html">Admin</a></li>
        </ul>


        <div class="page-header">
            <h1>Insert
                <small>Add a movie to the database</small>
            </h1>
        </div>

        <form method="post" role="form">
            <div class="form-group">
                <label>
                    Title:
                    <input type="text" class="form-control" name="title"
                           value="<?php echo !isset($response) ? $title : ""; ?>"/>
                </label>
            </div>
            <div class="form-group">
                <label>
                    Director:
                    <input type="text" class="form-control" name="director"
                           value="<?php echo !isset($response) ? $director : ""; ?>"/>
                </label><br/>
            </div>
            <div class="form-group">
                <label>
                    Shakiness:
                    <input type="text" class="form-control" name="shakiness"
                           value="<?php echo !isset($response) ? $shakiness : ""; ?>"/>
                </label>
            </div>
            <input type="submit" value="Submit Movie" class="btn btn-default">
        </form>
        <span class="error"><?php echo $error ?> </span>
        <?php echo isset($response) ? $response : ""; ?>
    </div>
    </body>
    </html>
<?php
} else {
    if (!$error) {
        echo "Thanks for submitting some data! Here's what you submitted:
Title: $title Director: $director Shakiness: $shakiness";
    } else {
        echo 'There was an error submitting your data. Please make sure that "shakiness" was a number before continuing.';
    }
}
?>