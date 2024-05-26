<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Add custom styles here */
        .blur-bg {
          
            backdrop-filter: blur(5px);
        }
    </style>
</head>
<body class="bg-primary text-primary">
<?php include '../pages/header.php'; ?>
    <div class="container mx-auto flex justify-center items-center h-screen bg-secondary">
        <div class="w-full max-w-md bg-tertiary p-8 rounded-lg shadow-lg">
            <h1 class="text-4xl font-bold text-center mb-4">Register</h1>
            <form method="post" action="register.php">
                <input type="text" name="username" id="username" placeholder="Username" class="w-full px-4 py-2 mb-4 bg-gray-900 rounded-lg text-white" required><br>
                <input type="email" name="email" id="email" placeholder="Email" class="w-full px-4 py-2 mb-4 bg-gray-900 rounded-lg text-white" required><br>
                <input type="password" name="password" id="password" placeholder="Password" class="w-full px-4 py-2 mb-4 bg-gray-900 rounded-lg text-white" required><br>
                <input type="submit" name="submit" value="Register" class="w-full px-4 py-2 bg-red-700 text-white font-bold rounded-lg cursor-pointer hover:bg-red-600">
            </form>
            <h1 class="text-lg font-bold my-4">Or</h1>
            <a href="login.php" class="text-red-500 font-bold">Log in</a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-red-700 py-4 px-8 text-center mt-8 ">
        <!-- Footer content here -->
        &copy; 2024 Your Company
    </footer>
</body>
</html>

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
