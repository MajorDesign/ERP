<?php
$pdo = new PDO("mysql:host=localhost;dbname=banco_projeto", "root", "");

$status = $_POST['status'];

$pdo->prepare("UPDATE produtos SET status = ? WHERE id = 1")->execute([$status]);

header("Location: index.php");
exit;
