<?php 

include 'config.php';

$username = htmlspecialchars($_POST['username']);
$email = htmlspecialchars($_POST['email']);
$password = htmlspecialchars(mysqli_real_escape_string($link, $_POST['password']));

$hash = password_hash($password, PASSWORD_DEFAULT);

$save = mysqli_query($link, "INSERT INTO users (id, username, email, password) VALUES ('', '$username', '$email', '$hash')");

 ?>