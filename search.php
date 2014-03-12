<?php
include_once("shell.php");

$mapToColumnNames = array(
    'titl' => "numOfTitle",
    'maxi' => "numOfMax",
    'mini' => "numOfMin",
    'dire' => "numOfDir",
    "ever" => "numOfAll"
);


$mapToNames = array(
    'titl' => "Title",
    'maxi' => "Maximum Shakiness",
    'mini' => "Minimum Shakiness",
    'dire' => "Director",
    "ever" => "Everything"
);

if (!isset($_GET['submit'])) {
    $_GET['input-text'] = "";
    $_GET['type'] = "titl";
}

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
    <h1>Hello Movie goers! <a class="btn btn-primary btn-xs pull-right" href="Admin/login.html">Log in</a></h1>

    <p>This site is intended to help you track various pieces of data about different times in movies. Is there a
        spot of shakiness that could make some people sick? How about a gory scene? Maybe some nudity you would
        rather avoid? You can find exactly when you should look away (and even a short description of the missed plot)
        with this service. Currently, we are in the pre-alpha stage and what you see are some very
        simple proof of concepts and learning exercises. Eventually something more will be added.</p>
</div>

<div class="container text-center">

    <ul class="nav nav-pills">
        <li><a href="index.php">Insert Movie</a></li>
        <li class="active"><a href="search.php">Search</a></li>
        <li><a href="browse.php">Browse</a></li>
        <li><a href="Admin/index.html">Admin</a></li>
    </ul>

    <div class="page-header">
        <h1>Search
            <small>find a movie in the database</small>
        </h1>
    </div>
    <form id="gen-form" class="form-inline" method="get">
        <div class="row">
            <div class="col-md-12">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search by..." name="input-text"
                           value="<?php echo getEscapedGET('input-text'); ?>" id="input-text">

                    <div class="input-group-btn text-left">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                id="dropdown-1"><?php echo $mapToNames[getEscapedGET('type')] == "" ? "Title" : $mapToNames[getEscapedGET('type')]; ?>
                            <span class="caret"></span></button>
                        <ul class="dropdown-menu pull-right">
                            <li><a href="#" class="dropdown-link" ref="1">Title</a></li>
                            <li><a href="#" class="dropdown-link" ref="1">Director</a></li>
                            <li><a href="#" class="dropdown-link" ref="1">Maximum Shakiness</a></li>
                            <li><a href="#" class="dropdown-link" ref="1">Minimum Shakiness</a></li>
                            <li><a href="#" class="dropdown-link" ref="1">Everything</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <input type="hidden" name="type" id="type" value="">
        </div>
        <br/>
        <button type="submit" name="submit" class="btn btn-default" id="submit-button">
            Submit Query
        </button>
    </form>

    <br/>

    <?php

    function makeLike($searchParameters)
    {
        $like = "";
        if (count($searchParameters) == 1) {
            if (trim($searchParameters[0]) != "") {
                $like = $like . "%" . $searchParameters[0] . "%";
                return $like;
            }
            return $like;
        } else {
            for ($i = 0; $i < count($searchParameters); $i++) {
                if ($i == 0) {
                    $like = $like . "%" . $searchParameters[$i] . "% ";
                } elseif ($i == (count($searchParameters) - 1)) {
                    $like = $like . " %" . $searchParameters[$i] . "%";
                } else {
                    $like = $like . " %" . $searchParameters[$i] . "% ";
                }
            }
            return trim($like);
        }
    }

    if (isset($_GET['submit']) && !$mysqli_err) {
        $searchParameters = explode(" ", getEscapedGET('input-text'));


        if (count($searchParameters) == 0 || $_GET['type'] == "ever") {
            if (!($stmt = $db_server->prepare("SELECT title, director, shakiness FROM movies"))) {
                $err = "failed to connect to the database, please try again later";
            }
        } elseif ($_GET['type'] == "mini" && is_numeric($searchParameters[0])) {
            if ($stmt = $db_server->prepare("SELECT title, director, shakiness FROM movies WHERE shakiness >= ?")) {
                $stmt->bind_param("i", $searchParameters[0]);
            } else {
                $err = "failed to connect to the database, please try again later";
            }
        } elseif ($_GET['type'] == "maxi" && is_numeric($searchParameters[0])) {
            if ($stmt = $db_server->prepare("SELECT title, director, shakiness FROM movies WHERE shakiness <= ?")) {
                $stmt->bind_param("i", $searchParameters[0]);
            } else {
                $err = "failed to connect to the database, please try again later";
            }
        } elseif ($_GET['type'] == "titl") {

            #Current problem: Does not automatically fall back to everything if there are no results
            $likeTitl = makeLike($searchParameters);
            if ($stmt = $db_server->prepare("SELECT title, director, shakiness FROM movies WHERE title LIKE ?")) {
                $stmt->bind_param("s", $likeTitl);
            } else {
                $err = "failed to connect to the database, please try again later";
            }
        } elseif ($_GET['type'] == "dire") {
            #Current problem: Does not automatically fall back to everything if there are no results
            $likeDire = makeLike($searchParameters);
            if ($stmt = $db_server->prepare("SELECT title, director, shakiness FROM movies WHERE director LIKE ?")) {
                $stmt->bind_param("s", $likeDire);
            } else {
                $err = "failed to connect to the database, please try again later";
            }
        } else {
            $_GET['type'] = "ever";
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
            logger($stmt->error);
        }

        $stmt->bind_result($title, $director, $shakiness);

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

        $col = $mapping[$_GET['type']];

        $dbQuery = "UPDATE numOfSearches SET $col = $col + 1";

        if (!$db_server->query($dbQuery)) {
            logger("Failed to increment number of searches. Computed column: " . $col . ", err: " . $db_server->error);
        }

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