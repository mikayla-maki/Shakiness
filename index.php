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
            $response = True;
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
            rather avoid? You can find exactly when you should look away (and even a short description of the missed
            plot)
            with this service. Currently, we are in the pre-alpha stage and what you see are some very
            simple proof of concepts and learning exercises. Eventually something more will be added.</p>
    </div>

    <div class="container text-center">

        <ul class="nav nav-pills">
            <li class="active"><a href="index.php">Insert Movie</a></li>
            <li><a href="search.php">Search</a></li>
            <li><a href="browse.php">Browse</a></li>
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
        <?php if (isset($response)) { ?>
            <div class="row">
                <div class="col-md-4">
                </div>
                <div class="col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <?php echo $title ?>, by <?php echo $director ?>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <!-- Get this image from the filesystem or the database -->
                                    <img src="http://placehold.it/93x108" alt="Image here!"
                                         class="img-thumbnail img-responsive">
                                </div>
                                <div class="col-md-8">
                                    <h4>Data:</h4>

                                    <p>
                                        Shakiness: <?php echo $shakiness ?>
                                        <br/>OTHER STATISTICS HERE
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Description:</h4>

                                    <p>
                                        TO BE ADDED
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                </div>
            </div>
        <?php
        }
        ?>
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