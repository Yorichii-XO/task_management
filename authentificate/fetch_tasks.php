<?php
// Include database connection
include './connexion.php';

// Check if user ID is provided
if(isset($_GET['user_id'])) {
    $userId = $_GET['user_id'];

    // Fetch tasks for the selected user
    $stmt = $con->prepare("SELECT * FROM tasks WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Generate HTML for displaying tasks
    if(!empty($tasks)) {
        $html = "<h3>Tasks for Selected User</h3>";
        $html .= "<table>";
        $html .= "<tr><th>Task ID</th><th>Task Description</th><th>Status</th></tr>";
        foreach($tasks as $task) {
            $html .= "<tr><td>{$task['task_id']}</td><td>{$task['description']}</td><td>{$task['status']}</td></tr>";
        }
        $html .= "</table>";
    } else {
        $html = "<p>No tasks found for the selected user.</p>";
    }

    // Output HTML
    echo $html;
} else {
    echo "User ID not provided.";
}
?>
