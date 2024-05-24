<?php
session_start();
require_once '../connexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT users.ID_user, users.username, users.password, users.email, roles.role_type 
            FROM users 
            JOIN roles ON users.ID_role = roles.ID_role 
            WHERE email = :email";
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify the password
        if ($password === $row['password']) { // Using plain text password for demo purposes
            // Set session variables for authentication
            $_SESSION['ID_user'] = $row['ID_user'];

            $_SESSION['username'] = $row['username'];
            $_SESSION['role_type'] = $row['role_type'];
            $_SESSION['email'] = $row['email'];

            // Set cookies for user preferences (non-sensitive information)
            setcookie('username', $row['username'], time() + (86400 * 30), "/"); // 30 days
            // Commented out email cookie set line
             setcookie('email', $row['email'], time() + (86400 * 30), "/"); // 30 days
            setcookie('role_type', $row['role_type'], time() + (86400 * 30), "/"); // 30 days

            // Redirect based on user role
            if ($row['role_type'] == 'admin') {
                header("Location: dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            // Incorrect password
            echo "Invalid password.";
        }
    } else {
        // User not found
        echo "No user found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <form method="post" action="login.php">
        <input type="email" name="email" id="email" placeholder="Email" required><br>
        <input type="password" name="password" id="password" placeholder="Password" required><br>
        <input type="submit" name="submit" value="Login"><br>
    </form>
    <h1>Or</h1>
    <a href="register.php">Register</a>

    <!-- <h2>Check Stored Cookies</h2>
    <p>Username Cookie: <?php echo isset($_COOKIE['username']) ? $_COOKIE['username'] : 'Not set'; ?></p>
    <p>Email Cookie: <?php echo isset($_COOKIE['email']) ? $_COOKIE['email'] : 'Not set'; ?></p>
    <p>Role Type Cookie: <?php echo isset($_COOKIE['role_type']) ? $_COOKIE['role_type'] : 'Not set'; ?></p> -->
</body>
</html>
