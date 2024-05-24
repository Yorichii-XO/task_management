<?php
require_once '../connexion.php'; // Include your database connection file here

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task_title = $_POST['task_title'];
    $task_description = $_POST['task_description'];
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];
    $is_completed = isset($_POST['is_completed']) ? 1 : 0;
    $ID_category = $_POST['ID_category'];

    // SQL query to insert task into database
    $sql = "INSERT INTO tasks (task_title, task_description, priority, due_date, is_completed, ID_category) 
            VALUES (:task_title, :task_description, :priority, :due_date, :is_completed, :ID_category)";

    try {
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':task_title', $task_title);
        $stmt->bindParam(':task_description', $task_description);
        $stmt->bindParam(':priority', $priority);
        $stmt->bindParam(':due_date', $due_date);
        $stmt->bindParam(':is_completed', $is_completed);
        $stmt->bindParam(':ID_category', $ID_category);
        $stmt->execute();
        echo "Task added successfully";
    } catch (PDOException $e) {
        echo "Error adding task: " . $e->getMessage();
    }
}
?>
