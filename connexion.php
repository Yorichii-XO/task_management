<?php
// Database connection
try {
    $con = new PDO('mysql:host=localhost;dbname=task_db', 'root', "");
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "good"; // Uncomment this line if you want to confirm the connection
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
