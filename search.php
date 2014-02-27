<?php
include_once("shell.php");

$pageData = '
    <br/>
    <form>
        <div class="form-inline">
            <label for="search">Search for movies: </label>
            <input type="text" name="search">
        </div>
    </form>
';

if (isset($_POST['search'])) {
    $like = "";
    $searchParameters = explode(" ", getEscapedPost('search'));
    foreach ($searchParameters as $parameter) {
        $like = $like . " %" . $parameter . "% ";
    }
    $query = "SELECT * FROM MOVIES WHERE director LIKE $like OR title LIKE $like";
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
                    <th>Title<</th>
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