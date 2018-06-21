<!DOCTYPE html>
<html>
<head>
<title>Persian Calendar</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<h1 class="title">Persian Calendar</h1>

<?php
    include 'calendar.php';
    $calendar = new Calendar();
    echo $calendar->show();
?>
</body>
</html>
