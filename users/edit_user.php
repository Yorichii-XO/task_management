<?php
// Include the database connection script
include './connexion.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if all required fields are present in the POST data
    if (isset($_POST['ID_user'], $_POST['username'], $_POST['email'], $_POST['password'], $_POST['ID_role'])) {
        // Get data from POST request
        $userId = $_POST['ID_user']; // Corrected to match the key sent from JavaScript
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $roleId = $_POST['ID_role']; // Corrected to match the key sent from JavaScript

        // Update the user in the database
        try {
            $stmt = $con->prepare("UPDATE users SET username = :username, email = :email, password = :password, ID_role = :roleId WHERE ID_user = :userId");
            $result = $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password' => $password,
                ':roleId' => $roleId,
                ':userId' => $userId
            ]);

            // Check if the update was successful
            if ($result) {
                // Return success response
                echo 'success';
            } else {
                // Return error response
                echo 'error';
            }
        } catch (PDOException $e) {
            // Handle database error
            echo 'Database error: ' . $e->getMessage();
        }
    } else {
        // Return error response if required fields are missing
        echo 'Required fields are missing';
    }
} else {
    // Handle invalid request method (optional)
    echo 'Invalid request method';
}
?>
