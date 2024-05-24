<?php
// Include database connection
include './connexion.php';

// Fetch users from the database
$stmt = $con->query("SELECT ID_user, username FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Output users as JSON
echo json_encode($users);
?>
