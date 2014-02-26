<?php
include_once("shell.php");

$pageData = '
    <br/>
    A fancy SELECT Builder by the one and only trentonmaki.us! Our motto is: if it doesn\'t work it\'s MySQL\'s fault!<br/>
    <b>Select only:</b>

    <form method="post" class="form-inline">
        <div class="form-group">
            <label>
                <input type="checkbox" name="selectTitle"/>
                Title
            </label>
        </div>
        <div class="form-group">
            <label>
                <input type="checkbox" name="selectDirector"/>
                Director
            </label>
        </div>
        <div class="form-group">
            <label>
                <input type="checkbox" name="selectShakiness"/>
                Shakiness
            </label>
        </div>
        <br/>
        <b>Where:</b>
        <br/>

        <div class="form-group">
            <label>
                <input type="checkbox" name="matchTitle">
                Title
            </label>
        </div>
        <div class="form-group">
            <label>
                <input type="checkbox" name="matchDir">
                Director
            </label>
        </div>
        <br/>
        Contains:
        <div class="form-group">
            <label>
                <input type="text" name="contains">
            </label>
        </div>
        , exclude from those results anything that matches:
        <div class="form-group">
            <label>
                <input type="text" name="excludes">
            </label>
        </div>
        , and where shakiness is
        <div class="form-group">
            <label>
                <select name="compare">
                    <option value="null">Select an option...</option>
                    <option value="gt">Greater than</option>
                    <option value="lt">Less than</option>
                    <option value="eq">Equal to</option>
                    <option value="neq">Not equal to</option>
                </select>
            </label>
        </div>
        <div class="form-group">
            <label>
                <input type="text" name="shakeNum">
            </label>
        </div>
        <br/>

        <div class="form-group">
            <input type="submit" value="Submit Query" class="btn btn-primary">
        </div>
    </form>
';

$select = "SELECT ";
$selectExists = false;
$query = "";
$numOfColumns = 0;
$titleExists = false;
$directorExists = false;
$shakinessExists = false;

if (isset($_POST["selectTitle"])) {
    $numOfColumns++;
    $selectExists = true;
    $titleExists = true;
    $select = $select . " title";
}
if (isset($_POST["selectDirector"])) {
    if ($selectExists) {
        $select = $select . ", director";
    } else {
        $select = $select . "director";
    }
    $numOfColumns++;
    $directorExists = true;
    $selectExists = true;
}
if (isset($_POST["selectShakiness"])) {
    if ($selectExists) {
        $select = $select . ", shakiness";
    } else {
        $select = $select . "shakiness";
    }
    $numOfColumns++;
    $selectExists = true;
    $shakinessExists = true;
}
if (!$selectExists) {
    $numOfColumns = 3;
    $select = $select . " title, director, shakiness ";
    $selectExists = true;
    $shakinessExists = true;
    $directorExists = true;
    $titleExists = true;
}
$from = " FROM movies";
$where = " WHERE ";
$matched = false;
//MATCH
if ((isset($_POST["matchTitle"]) || isset($_POST["matchDir"])) && (isset($_POST["contains"]) || isset($_POST["excludes"]))) {
    $matched = true;
    $where = $where . "MATCH(";
    if (isset($_POST["matchTitle"])) {
        $where = $where . "title";
        if (isset($_POST["matchDir"])) {
            $where = $where . ",director";
        }
    } elseif (isset($_POST["matchDir"])) {
        $where = $where . "director";
    }
    $where = $where . ") AGAINST('";
    $includes = "";
    if (isset($_POST["contains"])) {
        if (trim(getEscapedPost("contains"))) {
            $contains = preg_split('/[\s]+/', getEscapedPost("contains"));
            foreach ($contains as $item) {
                $includes = $includes . " +" . $item . "*";
            }
        }
    }
    if (isset($_POST["excludes"])) {
        if (trim(getEscapedPost("excludes"))) {
            $excludes = preg_split('/[\s]+/', getEscapedPost("excludes"));
            foreach ($excludes as $item) {
                $includes = $includes . " -" . $item . "*";
            }
        }
    }
    $where = $where . $includes . "' IN BOOLEAN MODE) ";
}

if (isset($_POST["compare"]) && isset($_POST["shakeNum"])) {
    $compare = getEscapedPost("compare");
    $shakeNum = getEscapedPost("shakeNum");
    if ($compare != "null" and is_numeric($shakeNum)) {
        if ($matched) {
            $where = $where . " AND";
        }
        $where = $where . " shakiness ";
        if ($compare == "gt") {
            $where = $where . " > ";
        } elseif ($compare == "lt") {
            $where = $where . " < ";
        } elseif ($compare == "eq") {
            $where = $where . " = ";
        } elseif ($compare == "neq") {
            $where = $where . " <> ";
        }
        $where = $where . " " . $shakeNum;
    }
}

if (trim($where) == "WHERE") {
    $where = $where . " 1";
}

$query = $select . $from . $where . " LIMIT 0, 100";

logger("Query was: " . $query);

$result = false;
if ($query !== "") {
    $result = mysql_query($query, $db_server);
    if ($result === false) {
        logger("Failed to insert into the databse. Error: " . mysql_error() . "\n Query: " . $query);
        echo "<span class='error'>Failed to query the DB</span>";
    }
}
?>

<?php if ($result) { ?>
    <div class="table-responsive">
        <table class='data table'>
            <tr>
                <?php
                if ($titleExists) {
                    echo "<td><b>Title</b></td>";
                }
                if ($directorExists) {
                    echo "<td><b>Director</b></td>";
                }
                if ($shakinessExists) {
                    echo "<td><b>Shakiness</b></td>";
                }
                ?>

            </tr>
            <?php
            $numOfRows = mysql_num_rows($result);
            for ($i = 0; $i < $numOfRows; $i++) {
                $row = mysql_fetch_row($result); ?>
                <tr>
                    <?php
                    if ($numOfColumns >= 1) {
                        echo "<td>$row[0]</td>";
                    }
                    if ($numOfColumns >= 2) {
                        echo "<td>$row[1]</td>";
                    }
                    if ($numOfColumns >= 3) {
                        echo "<td>$row[2]</td>";
                    };?>
                </tr>
            <?php } ?>
        </table>
    </div>
<?php } ?>