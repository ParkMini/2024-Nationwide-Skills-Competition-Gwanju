<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "gwangju";

$pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);