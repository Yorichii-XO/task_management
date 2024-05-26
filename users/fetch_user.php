<?php
// Include the database connection script
include './connexion.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the user ID is provided in the POST data
    if (isset($_POST['id'])) {
        $userId = $_POST['id'];
        
        // Fetch user data based on the provided ID
        $stmt = $con->prepare("SELECT * FROM users WHERE ID_user = :id");
        $stmt->execute([':id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Check if user data was found
        if ($user) {
            // Generate HTML form with user data for editing
            echo "
                <input type='hidden' id='ID_user' value='{$user['ID_user']}'>
                <input type='text' id='username' value='{$user['username']}' class='swal2-input' placeholder='Username'>
                <input type='email' id='email' value='{$user['email']}' class='swal2-input' placeholder='Email'>
                <input type='password' id='password' value='{$user['password']}' class='swal2-input' placeholder='Password'>
                <select id='role' class='swal2-select'>";
                    foreach ($roles as $role) {
                        $selected = ($role['ID_role'] == $user['ID_role']) ? 'selected' : '';
                        echo "<option value='{$role['ID_role']}' {$selected}>{$role['role_type']}</option>";
                    }
            echo "</select>";
        } else {
            // Return error response if user data not found
            echo "<p>User not found</p>";
        }
    } else {
        // Return error response if user ID is not provided
        echo "<p>User ID not provided</p>";
    }
} else {
    // Return error response if request method is not POST
    echo "<p>Invalid request method</p>";
}
?>
