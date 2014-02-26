<?php
require_once("login.php");
$chartData = array();

$data = mysql_query("SELECT date_format(timestamp,'%Y-%m-%d') AS time, count(*) AS numOfMoviesAtTime FROM movies GROUP BY time", $db_server);

$rows = mysql_num_rows($data);

for ($j = 0; $j < $rows; ++$j) {
    $time = strtotime(mysql_result($data, $j, 'time') . " UTC") * 1000;
    $numOfMoviesAtTime = intval(mysql_result($data, $j, 'numOfMoviesAtTime'));
    array_push($chartData, array($time, $numOfMoviesAtTime));
}
$chartData_js = json_encode($chartData);
?>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="../bootstrap/js/jquery.js"></script>
    <link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap-theme.min.css" media="screen"/>
    <link rel="stylesheet" type="text/css" href="../bootstrap/css/bootstrap.min.css" media="screen"/>
    <script src="../bootstrap/js/plugins/flot/jquery.flot.js"></script>
    <script src="../bootstrap/js/plugins/flot/jquery.flot.time.js"></script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin</title>

    <script>

        $(function () {
            var data = [
                    {
                        label: "Inserts by day",
                        data: <?php echo $chartData_js; ?>
                    }
                ]
                ;
            var options = {
                xaxis: {
                    mode: "time",
                    timeformat: "%Y/%m/%d",
                    tickSize: [1, "day"]

                },
                series: {
                    lines: {
                        show: true
                    },
                    points: {
                        radius: 3,
                        show: true,
                        fill: true
                    }
                }
            };
            var chart = $("#flot-chart");
            var x = $.plot(chart, data, options);
            console.log('Finished declaring Variables');
            console.log(JSON.stringify(chart));
            console.log(JSON.stringify(data));
            console.log(JSON.stringify(options));
            console.log(JSON.stringify(x));


        });
    </script>
</head>

<body>
<div id="header" class="text-center">
    <a href="./../index.php" class="btn btn-primary">Return to site</a>
</div>
<br/>
<br/>

<div id="flot-chart" style="height:400px;width:600px;margin-left:auto;margin-right:auto;">
</div>
</body>
</html>

