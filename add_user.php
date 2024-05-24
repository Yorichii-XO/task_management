<?php
include './connexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $stmt = $con->prepare("INSERT INTO users (username, email, password, ID_role) VALUES (:username, :email, :password, :role)");
    $result = $stmt->execute([
        ':username' => $username,
        ':email' => $email,
        ':password' => $password,
        ':role' => $role
    ]);

    if ($result) {
        echo 'success';
    } else {
        echo 'error';
    }
}
?>
