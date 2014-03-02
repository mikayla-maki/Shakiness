<?php
include_once("shell.php");

$pageData = '
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
';

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

    $pageData = $pageData . '
        <div class="table-responsive">
            <table class="data table">
                <tr>
                    <td><b>Title</b></td>
                    <td><b>Director</b></td>
                    <td><b>Shakiness</b></td>
                </tr>';
    while ($stmt->fetch()) {
        $pageData = $pageData . "
                    <tr>
                        <td>$title</td>
                        <td>$director</td>
                        <td>$shakiness</td>
                    </tr>";
    }
    $pageData = $pageData . '</table>
        </div>';
    $stmt->close();
    $db_server->close();

} else if (isset($err)) {
    $pageData = $pageData . "<span style='color:red'>Could not connect to database</span>";
    logger($err);
}

printToPage($pageData);
?>