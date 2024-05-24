<?php
require_once '../connexion.php'; // Include your database connection file here

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task_id = $_POST['task_id'];
    $task_title = $_POST['task_title'];
    $task_description = $_POST['task_description'];
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];
    $is_completed = isset($_POST['is_completed']) ? 1 : 0;
    $ID_category = $_POST['ID_category'];

    // SQL query to update task in the database
    $sql = "UPDATE tasks SET task_title = :task_title, task_description = :task_description, priority = :priority, 
            due_date = :due_date, is_completed = :is_completed, ID_category = :ID_category WHERE task_id = :task_id";

    try {
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':task_title', $task_title);
        $stmt->bindParam(':task_description', $task_description);
        $stmt->bindParam(':priority', $priority);
        $stmt->bindParam(':due_date', $due_date);
        $stmt->bindParam(':is_completed', $is_completed);
        $stmt->bindParam(':ID_category', $ID_category);
        $stmt->bindParam(':task_id', $task_id);
        $stmt->execute();
        echo "Task updated successfully";
    } catch (PDOException $e) {
        echo "Error updating task: " . $e->getMessage();
    }
}
?>
