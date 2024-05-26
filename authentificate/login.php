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
            $_SESSION['ID_role'] = $row['ID_role'];
            $_SESSION['email'] = $row['email'];

            setcookie('username', $row['username'], time() + (86400 * 30), "/"); // 30 days
            setcookie('email', $row['email'], time() + (86400 * 30), "/"); // 30 days
            setcookie('role_type', $row['role_type'], time() + (86400 * 30), "/"); // 30 days

            // Redirect based on user role
            if ($row['role_type'] == 'admin') {
                header("Location: /task-management/main.php");
            } else {
                header("Location: /task-management/main.php");
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
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-primary text-primary">

    <?php include '../pages/header.php'; ?>
    <div class="container mx-auto flex justify-center items-center h-screen bg-secondary">
        <div class="w-full max-w-md bg-tertiary p-8 rounded-lg shadow-lg">
            <h1 class="text-4xl font-bold text-center mb-4">Login</h1>
            <form method="post" action="login.php">
                <input type="email" name="email" id="email" placeholder="Email" class="w-full px-4 py-2 mb-4 bg-gray-900 rounded-lg text-white" required><br>
                <input type="password" name="password" id="password" placeholder="Password" class="w-full px-4 py-2 mb-4 bg-gray-900 rounded-lg text-white" required><br>
                <input type="submit" name="submit" value="Login" class="w-full px-4 py-2 bg-red-700 text-white font-bold rounded-lg cursor-pointer hover:bg-red-600">
            </form>
            <h1 class="text-lg font-bold my-4">Or</h1>
            <a href="register.php" class="text-red-500 font-bold">Register</a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-red-700 py-4 px-8 text-center mt-8">
        <!-- Footer content here -->
        &copy; 2024 Your Company
    </footer>
</body>

</html>