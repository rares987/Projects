<?php
session_start();

$pdo = new PDO('sqlite:database.db');
$name = $_POST['name'];
$email = $_POST['email'];
$comment = $_POST['text'];
$statement = $pdo->prepare(
    "INSERT INTO feedback (name, email, comment) VALUES (:name, :email, :comment)"
);
$statement->execute(array(
    ':name' => $name,
    ':email' => $email,
    ':comment' => $comment
));
echo "succes";
?>