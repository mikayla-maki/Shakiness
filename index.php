<?php
include_once("shell.php");

$pageData = "";
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
        if ($stmt = $db_server->prepare("INSERT INTO movies(title,director,shakiness) VALUES (?,?,?)")) {
            $stmt->bind_param("ssi", $title, $director, $shakiness);
            $stmt->execute();
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
            <td>' . $title . '</td>
            <td>' . $director . '</td>
            <td>' . $shakiness . '</td>
        </tr>
    </table>
';
        } else {
            logger("Failed to insert: " . $db_server->errno);
            $error = "failed to submit to the database, please try again later";
        }
    }
}

if ($sender != "curl") {
    $pageData = $pageData . '
    <h2>Add a movie!</h2>
    <form method="post" role="form">
        <div class="form-group">
            <label>
                Title:
                <input type="text" class="form-control" name="title" value="';
    if (!isset($response)) {
        $pageData = $pageData . $title;
    }
    $pageData = $pageData . '"/>
            </label>
        </div>
        <div class="form-group">
            <label>
                Director:
                <input type="text" class="form-control" name="director" value="';
    if (!isset($response)) {
        $pageData = $pageData . $director;
    }
    $pageData = $pageData . '"/>
            </label><br/>
        </div>
        <div class="form-group">
            <label>
                Shakiness:
                <input type="text" class="form-control" name="shakiness" value="';
    if (!isset($response)) {
        $pageData = $pageData . $shakiness;
    }
    $pageData = $pageData . '"/> </label> </div>
        <input type="submit" class="btn btn-default">
    </form> <span class="error">' . $error . '</span>';
    if (isset($response)) {
        $pageData = $pageData . $response;
    }
    printToPage($pageData);
} else {
    if (!$error) {
        printToPage("Thanks for submitting some data! Here's what you submitted:
Title: $title Director: $director Shakiness: $shakiness");
    } else {
        printToPage('There was an error submitting your data. Please make sure that "shakiness" was a number before continuing.');
    }
}
?>