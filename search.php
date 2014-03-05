<?php
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
        <script>
            $(document).ready(function () {
                $("#gen-form").submit(function (e) {

                    var typeJQSel = $("#type");
                    e.preventDefault();
                    var inputJQSel = $('input[name="input-text"]');
                    typeJQSel.value = $("#dropdown-1").html().substring(0, 4).trim().toLowerCase();
                    alert(inputJQSel.val());
                    alert(typeJQSel.value);
                    var post = $.post("search.php", {'inputText': inputJQSel.val(), 'type': typeJQSel.value});
                    post.done(function (data) {
                        $("html").replaceWith(data);
                    });
                    return false;
                });
        </script>
    </head>
    <body>
    <div class="jumbotron">
        <h1>Hello Movie goers!</h1>

        <p>This site is intended to help you track various pieces of data about different times in movies. Is there a
            spot of shakiness that could make some people sick? How about a gory scene? Maybe some nudity you would
            rather avoid? You can find exactly when you should look away and even a short description of the plot in
            those sections with this service. Currently we are in the pre-alpha stage and what you see are some very
            simple proof of concepts and learning exercises. Eventually something more will be added</p>
    </div>

    <ul class="nav nav-tabs">
        <li><a href="index.php">Insert Movie</a></li>
        <li class="active"><a href="search.php">Search</a></li>
        <li><a href="Admin/index.php">Admin</a></li>
        <li><a class="btn btn-primary btn-xs pull-right" href="#">Log in</a></li>
    </ul>

    <div class="container-fluid text-center">

        <div class="page-header">
            <h1>Search
                <small>find a movie in the database</small>
            </h1>
        </div>

        <form id="gen-form" class="form-inline" method="post">
            <div class="row">
                <div class="col-lg-6">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search by..." name="input-text">

                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                    id="dropdown-1">Maximum Shakiness <span class="caret"></span></button>

                            <ul class="dropdown-menu pull-right">
                                <li><a href="#" class="dropdown-link" ref="1">Maximum Shakiness</a></li>
                                <li><a href="#" class="dropdown-link" ref="1">Minimum Shakiness</a></li>
                                <li><a href="#" class="dropdown-link" ref="1">Title or Director</a></li>
                                <li><a href="#" class="dropdown-link" ref="1">Everything</a></li>
                            </ul>
                            <input type="submit" class="btn btn-default" type="button"/>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="type" id="type">
        </form>


        <br/>

        <form class="form-inline" method="post" role="form">
            <div class="form-group">
                <label class="sr-only" for="search">Search by director or title...</label>
                <input type="text" name="search" placeholder="Search by director or title...">
            </div>
            <button type="submit" class="btn btn-default">Search!</button>
        </form>
        <form class="form-inline" method="post" role="form">
            <div class="form-group">
                <label class="sr-only" for="searchShake">Search by maximum shakiness...</label>
                <input type="text" name="searchShake" placeholder="Search by maximum shakiness...">
            </div>
            <button type="submit" class="btn btn-default">Search!</button>
        </form>
        <form class="form-inline" method="post" role="form">
            <input type="hidden" name="all" value="true">
            <input type="submit" class="btn btn-default" value="Get all results">
        </form>
    </div>
    </body>
    </html>

<?php
logger("php is being evaluated");
echo json_encode($_POST);
echo "<h1> DEFL: " . htmlspecialchars($_POST["type"]) . "</h1>";
echo "<h1> MAXI: " . htmlspecialchars($_POST["input-text"]) . "</h1>";

if (isset($_POST['search']) && !$mysqli_err) {
    $query = "SELECT title, director, shakiness FROM movies WHERE director LIKE ? OR title LIKE ?";
    $like = "";
    $searchParameters = explode(" ", getEscapedPost('search'));

    if (count($searchParameters) == 1) {
        if (trim($searchParameters[0]) != "") {
            $like = $like . "%" . $searchParameters[0] . "%";
        } else {
            $select_all = true;
        }
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
//        $stmt = $db_server->prepare($query);
//        $stmt->bind_param("s", $like);
//        $stmt->bind_param("s", $like);
    }

    if (!isset($select_all) && !$select_all) {
        if ($stmt = $db_server->prepare($query)) {
            $stmt->bind_param('ss', $like, $like);
        } else {
            $err = "failed to connect to the database, please try again later";
        }
    }

} elseif (isset($_POST['searchShake']) && !$mysqli_err) {
    $searchParameters = explode(" ", getEscapedPost('searchShake'));
    if (count($searchParameters) == 0) {
        $select_all = true;
    } elseif (count($searchParameters) != 1 || !is_numeric($searchParameters[0])) {
        $err = "bad parameters to a shakiness search";
        logger("bad parameters to a shakiness search: '" . json_encode($searchParameters) . "'");
    } else {
        $query = "SELECT title, director, shakiness FROM movies WHERE shakiness <= ?";
    }

    if (!isset($select_all) || !$select_all) {
        if ($stmt = $db_server->prepare($query)) {
            $stmt->bind_param("i", $searchParameters[0]);
        } else {
            $err = "failed to connect to the database, please try again later";
        }
    }

} elseif ((isset($_POST["all"]) || (isset($select_all) && $select_all)) && !$mysqli_err) {
    $query = "SELECT title, director, shakiness FROM movies";
    if ($stmt = $db_server->prepare($query)) {
    } else {
        $err = "failed to connect to the database, please try again later";
    }
}

if (isset($stmt) && !isset($err) && !$mysqli_err) {

    $title = "";
    $director = "";
    $shakiness = "";

    $stmt->execute();

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
    logger($err);
    ?>
    <span style='color:red'>Could not connect to database</span>
<?php
}
?>