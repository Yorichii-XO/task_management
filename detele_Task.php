<?php
// Include the database connection script
include './connexion.php';

// Check if task ID is provided
if (isset($_POST['task_id'])) {
    $taskId = $_POST['task_id'];

    // Delete task from the database
    $stmt = $con->prepare("DELETE FROM tasks WHERE task_id = :task_id");
    $stmt->execute([':task_id' => $taskId]);

    // Check if task was deleted successfully
    if ($stmt->rowCount() > 0) {
        echo "Task deleted successfully.";
    } else {
        echo "Failed to delete task.";
    }
} else {
    echo "Task ID not provided.";
}
?>
