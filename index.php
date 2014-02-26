<?php
include_once("shell.php");

$sender = $_POST["sender"];

$error = "";
$title = "";
$director = "";
$shakiness = "";


if (isset($_POST["title"]) && isset($_POST["director"]) && isset($_POST["shakiness"])) {
    $title = getEscapedPost("title");
    $director = getEscapedPost("director");
    $shakiness = getEscapedPost("shakiness");
    if (!is_numeric($shakiness)) {
        $error = "Please enter a number for 'shakiness'";
        $shakiness = "";
    } else {
        $result = mysql_query("INSERT INTO movies (title, director, shakiness) VALUES ('{$title}', '{$director}', $shakiness)", $db_server);
        if ($result === false) {
            logger("Failed to insert into the databse. Error: " . mysql_error());

            $error = "Failed to submit the data";
        } else {
            $response = '
    <br />
    Thanks for the data! here is a summary:
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
            <td>$title</td>
            <td>$director</td>
            <td>$shakiness</td>
        </tr>
    </table>
';
        }
    }
}

if ($sender != "curl") {
    ?>
    <h2>Add a movie!</h2>
    <form method="post">
        <div class="form-group">
            <label>
                Title:
                <input type="text" class="form-control" name="title" value="<?php if (!isset($response)) {
                    echo $title;
                } ?>"/>
            </label>
        </div>
        <div class="form-group">
            <label>
                Director:
                <input type="text" class="form-control" name="director" value="<?php if (!isset($response)) {
                    echo $director;
                } ?>"/>
            </label><br/>
        </div>
        <div class="form-group">
            <label>
                Shakiness:
                <input type="text" class="form-control" name="shakiness" value="<?php if (!isset($response)) {
                    echo $shakiness;
                } ?>"/>
            </label>
        </div>
        <input type="submit" class="btn btn-default">
    </form>
    <?php echo "<span class='error'>" . $error . "</span>";
    if (isset($response)) {
        echo $response;
    }
} else {
    if (!$error) {
        printToPage("Thanks for submitting some data! Here's what you submitted:
Title: $title Director: $director Shakiness: $shakiness");
    } else {
        printToPage('There was an error submitting your data. Please make sure that "shakiness" was a number before continuing.');
    }
}
?>