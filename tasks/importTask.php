<?php
require_once '../connexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['tasks_json']) && $_FILES['tasks_json']['error'] === UPLOAD_ERR_OK) {
    $file_tmp = $_FILES['tasks_json']['tmp_name'];
    $json_data = file_get_contents($file_tmp);
    $tasks = json_decode($json_data, true);

    foreach ($tasks as $task) {
        $task_title = $task['task_title'];
        $task_description = $task['task_description'];
        $priority = $task['priority'];
        $due_date = $task['due_date'];
        $is_completed = $task['is_completed'];
        $ID_category = $task['ID_category'];

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
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        }
    }
    echo json_encode(['status' => 'success']);
}
?>
