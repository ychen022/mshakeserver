<?php
$db = mysql_connect("localhost", "root", "") or die(mysql_error());

mysql_select_db('mealshake', $db);