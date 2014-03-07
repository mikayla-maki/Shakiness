<?php
include_once("shell.php");
$er = error_reporting(E_ALL);
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
    <script src="js/Search.js"></script>
</head>
<body>
<div class="jumbotron">
    <h1>Hello Movie goers!</h1>

    <p>This site is intended to help you track various pieces of data about different times in movies. Is there a
        spot of shakiness that could make some people sick? How about a gory scene? Maybe some nudity you would
        rather avoid? You can find exactly when you should look away and even a short description of the plot in
        those sections with this service. Currently we are in the pre-alpha stage and what you see are some very
        simple proof of concepts and learning exercises. Eventually something more will be added.</p>
</div>

<ul class="nav nav-tabs">
    <li><a href="index.php">Insert Movie</a></li>
    <li class="active"><a href="search.php">Search</a></li>
    <li><a href="Admin/index.html">Admin</a></li>
    <li><a class="btn btn-primary btn-xs pull-right" href="#">Log in</a></li>
</ul>

<div class="container-fluid text-center">

    <div class="page-header">
        <h1>Search
            <small>find a movie in the database</small>
        </h1>
    </div>

    <form id="gen-form" class="form-inline" method="post">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Search by..." name="input-text">

            <div class="input-group-btn">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                        id="dropdown-1">Title or Director <span class="caret"></span></button>

                <ul class="dropdown-menu pull-right">
                    <li><a href="#" class="dropdown-link" ref="1">Maximum Shakiness</a></li>
                    <li><a href="#" class="dropdown-link" ref="1">Minimum Shakiness</a></li>
                    <li><a href="#" class="dropdown-link" ref="1">Title or Director</a></li>
                    <li><a href="#" class="dropdown-link" ref="1">Everything</a></li>
                </ul>

            </div>
        </div>
        <div class="input-group">
            <input type="submit" name="submit" class="btn btn-default" id="submit-button">
        </div>
        <input type="hidden" name="type" id="type" value="">
    </form>

    <br/>

    <?php

    if (isset($_POST['submit']) && !$mysqli_err) {
        $like = "";
        $searchParameters = explode(" ", getEscapedPost('input-text'));


        if (count($searchParameters) == 0 || $_POST['type'] == "ever") {
            if (!($stmt = $db_server->prepare("SELECT title, director, shakiness FROM movies"))) {
                $err = "failed to connect to the database, please try again later";
            }
        }
        if ($_POST['type'] == "mini" && is_numeric($searchParameters[0])) {
            if ($stmt = $db_server->prepare("SELECT title, director, shakiness FROM movies WHERE shakiness >= ?")) {
                $stmt->bind_param("i", $searchParameters[0]);
            } else {
                $err = "failed to connect to the database, please try again later";
            }
        }
        if ($_POST['type'] == "maxi" && is_numeric($searchParameters[0])) {
            if ($stmt = $db_server->prepare("SELECT title, director, shakiness FROM movies WHERE shakiness <= ?")) {
                $stmt->bind_param("i", $searchParameters[0]);
            } else {
                $err = "failed to connect to the database, please try again later";
            }
        }
        if ($_POST['type'] == "titl") {
            if (count($searchParameters) == 1) {
                if (trim($searchParameters[0]) != "") {
                    $like = $like . "%" . $searchParameters[0] . "%";
                }
            } else {
                for ($i = 0; $i < count($searchParameters); $i++) {
                    if ($i == 0) {
                        $like = $like . "%" . $searchParameters[$i];
                    } elseif ($i == (count($searchParameters) - 1)) {
                        $like = $like . "%" . $searchParameters[$i] . "%";
                    } else {
                        $like = $like . "%" . $searchParameters[$i];
                    }
                }
            }
            if ($stmt = $db_server->prepare("SELECT title, director, shakiness FROM movies WHERE director LIKE ? OR title LIKE ?")) {
                $stmt->bind_param("ss", $like, $like);
            } else {
                $err = "failed to connect to the database, please try again later";
            }
        } else {
            echo "reverting to  default: " . htmlspecialchars(json_encode($_POST));
            if (!($stmt = $db_server->prepare("SELECT title, director, shakiness FROM movies"))) {
                $err = "failed to connect to the database, please try again later";
            }
        }
    }
    if (isset($stmt) && !isset($err)) {

        $title = "";
        $director = "";
        $shakiness = "";

        if (!$stmt->execute()) {
            echo htmlspecialchars($stmt->error);
        }

        $stmt->bind_result($title, $director, $shakiness);

        $stmt->fetch();
        ?>
        <div class="table-responsive">
            <table class="data table">
                <tr>
                    <td><b>Title</b></td>
                    <td><b>Director</b></td>
                    <td><b>Shakiness</b></td>
                </tr>
                <?php
                while ($stmt->fetch()) {
                    ?>
                    <tr>
                        <td><?php echo $title ?></td>
                        <td><?php echo $director ?></td>
                        <td><?php echo $shakiness ?></td>
                    </tr>
                <?php
                }
                ?>
            </table>
        </div>
        <?php
        $stmt->close();
        $db_server->close();

    } else if (isset($err)) {
        logger("\$err: " . $err . " mysqli errors: " . $db_server->error);
        ?>
        <span style='color:red'>Could not connect to database</span>
    <?php
    }
    ?>
</div>
</body>
</html>