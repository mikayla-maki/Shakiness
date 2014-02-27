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
            <label class="sr-only" for="searchShake">Search by shakiness...</label>
            <input type="text" name="searchShake" placeholder="Search by maximum shakiness...">
        </div>
          <button type="submit" class="btn btn-default">Search!</button>
    </form>
';

if (isset($_POST['search'])) {
    $like = "";
    $searchParameters = explode(" ", getEscapedPost('search'));

    if (count($searchParameters) == 1) {
        $like = $like . "%" . $searchParameters[0] . "%";
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
    }
    $query = "SELECT * FROM movies WHERE director LIKE '$like' OR title LIKE '$like'";
} elseif (isset($_POST['searchShake'])) {
    $searchParameters = explode(" ", getEscapedPost('search'));
    if (count($searchParameters) != 1 || !is_numeric($searchParameters[0])) {
        logger("bad parameters to a shakiness search: " . var_export($searchParameters));
    } else {
        $query = "SELECT * FROM movies WHERE shakiness <= $searchParameters[0]";
    }
}
if (isset($query)) {
    logger("Query was: " . $query);

    $result = mysql_query($query, $db_server);

    if ($result === false) {
        logger("Failed to insert into the databse. Error: " . mysql_error() . "\n Query: " . $query);
        $pageData = $pageData . "<span class='error'>Failed to query the DB</span>";
    } else {
        $pageData = $pageData . '
        <div class="table-responsive">
            <table class="data table">
                <tr>
                    <th>Title</th>
                    <th>Director</th>
                    <th>Shakiness</th>
                </tr>';
        $numOfRows = mysql_num_rows($result);
        for ($i = 0; $i < $numOfRows; $i++) {
            $row = mysql_fetch_row($result);
            $pageData = $pageData . "
                    <tr>
                        <td>$row[0]</td>
                        <td>$row[1]</td>
                        <td>$row[2]</td>
                    </tr>";
        }
        $pageData = $pageData . '</table>
        </div>';
    }
}

printToPage($pageData);
?>