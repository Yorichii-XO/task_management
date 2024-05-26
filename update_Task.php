<?php
// Include the database connection script
include './connexion.php';

// Check if task ID and other necessary fields are provided
if (isset($_POST['task_id'], $_POST['task_title'], $_POST['task_description'], $_POST['priority'], $_POST['due_date'], $_POST['ID_category'], $_POST['ID_user'])) {
    $taskId = $_POST['task_id'];
    $taskTitle = $_POST['task_title'];
    $taskDescription = $_POST['task_description'];
    $priority = $_POST['priority'];
    $dueDate = $_POST['due_date'];
    $isCompleted = isset($_POST['is_completed']) ? 1 : 0;
    $categoryId = $_POST['ID_category'];
    $userId = $_POST['ID_user'];

    // Update task in the database
    $stmt = $con->prepare("UPDATE tasks SET task_title = :task_title, task_description = :task_description, priority = :priority, due_date = :due_date, is_completed = :is_completed, ID_category = :ID_category, ID_user = :ID_user WHERE task_id = :task_id");
    $stmt->execute([
        ':task_title' => $taskTitle,
        ':task_description' => $taskDescription,
        ':priority' => $priority,
        ':due_date' => $dueDate,
        ':is_completed' => $isCompleted,
        ':ID_category' => $categoryId,
        ':ID_user' => $userId,
        ':task_id' => $taskId
    ]);

    // Redirect back to manage tasks page
    header('Location: ./tasks.php');
    exit;
} else {
    echo "Required fields not provided.";
}
?>
