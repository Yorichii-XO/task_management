<?php
require_once '../connexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $ID_role = 2; // default role: user

    $sql = "INSERT INTO users (username, email, password, ID_role) VALUES (:username, :email, :password, :ID_role)";
    $stmt = $con->prepare($sql);

    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password); // Using plain text password for demo purposes
    $stmt->bindParam(':ID_role', $ID_role);

    if ($stmt->execute()) {
        echo "Registration successful";
    } else {
        echo "Error: " . $stmt->errorInfo()[2];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <h1>Register</h1>
    <form method="post" action="register.php">
        <input type="text" name="username" id="username" placeholder="Username" required><br>
        <input type="email" name="email" id="email" placeholder="Email" required><br>
        <input type="password" name="password" id="password" placeholder="Password" required><br>
        <input type="submit" name="submit" value="Register"><br>
    </form>
    <h1>Or</h1>
    <a href="login.php">Log in</a>
</body>
</html>
