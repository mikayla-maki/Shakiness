<?php
/**
 * Created by PhpStorm.
 * User: Huulktya
 * Date: 3/10/14
 * Time: 6:48 PM
 */


include_once("shell.php");


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
        <li><a href="search.php">Search</a></li>
        <li class="active"><a href="browse.php">Browse</a></li>
        <li><a href="Admin/index.html">Admin</a></li>
    </ul>

    <div class="page-header">
        <h1>Browse Movies
            <small>find a movie in the database manually!</small>
        </h1>
    </div>

    <?php
    #add this to the other pages
    if (!$stmt = $db_server->prepare("SELECT title, director, shakiness FROM movies")) {
        logger("Failed to query database, mysqli err: " . $stmt->error);
    }

    if (!$stmt->execute()) {
        logger($stmt->error);
    }

    $stmt->bind_result($title, $director, $shakiness);

    $result = $stmt->fetch();
    while ($result) {
        $counter = 0;
        ?>
        <div class="row">
            <?php
            while ($result && $counter < 3) {

                ?>
                <div class="col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <?php echo htmlspecialchars($title); ?>, by <?php echo htmlspecialchars($director); ?>
                        </div>
                        <div class="panel-body">
                            <!-- Get this image from the filesystem or the database -->
                            <img src="http://placehold.it/93x108" alt="Image here!"
                                 class="img-thumbnail img-responsive">

                            <p>
                                Shakiness: <?php echo htmlspecialchars($shakiness); ?>
                            </p>

                            <p>
                                Description: TO BE ADDED
                            </p>
                        </div>
                    </div>
                </div>
                <?php
                $counter = $counter + 1;
                $result = $stmt->fetch();
            }
            ?>
        </div>
    <?php
    }
    ?>
</div>
</body>
</html>