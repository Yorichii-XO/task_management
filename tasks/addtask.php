<?php
require_once '../connexion.php'; 
session_start();

if (isset($_POST['submit'])) {
    // Retrieve form data
    $task_title = $_POST['task_title'];
    $task_description = $_POST['task_description'];
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];
    $is_completed = isset($_POST['is_completed']) ? 1 : 0;
    $ID_category = $_POST['ID_category'];

    // Insert task into database
    $sql = "INSERT INTO tasks (task_title, task_description, priority, due_date, is_completed, ID_category) 
            VALUES (:task_title, :task_description, :priority, :due_date, :is_completed, :ID_category)";
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':task_title', $task_title);
    $stmt->bindParam(':task_description', $task_description);
    $stmt->bindParam(':priority', $priority);
    $stmt->bindParam(':due_date', $due_date);
    $stmt->bindParam(':is_completed', $is_completed);
    $stmt->bindParam(':ID_category', $ID_category);
    
    if ($stmt->execute()) {
        echo "<p>Task added successfully.</p>";
        header("Location: index.php");
        exit();    
    } else {
        echo "<p>Error adding task.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Task</title>
</head>
<body>
    <h1>Add Task</h1>

    <form action="" method="post">
        <label for="task_title">Task Title:</label>
        <input type="text" name="task_title" id="task_title" required><br><br>

        <label for="task_description">Task Description:</label>
        <input type="text" name="task_description" id="task_description" required><br><br>

        <label for="priority">Priority:</label>
        <select name="priority" id="priority" required>
            <?php 
            // Enum values for priority
            $priorities = ['Low', 'Medium', 'High'];
            foreach ($priorities as $priority) {
                echo "<option value=\"$priority\">$priority</option>";
            }
            ?>
        </select><br><br>

        <label for="due_date">Due Date:</label>
        <input type="date" name="due_date" id="due_date" required><br><br>

        <label for="category">Category:</label>
        <select name="ID_category" id="category" required>
            <?php 
            // Fetch categories from database
            $sql = "SELECT * FROM categories";
            $stmt = $con->query($sql);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value=\"{$row['category_id']}\">{$row['category_name']}</option>";
            }
            ?>
        </select><br><br>

        <input type="submit" name="submit" value="Add Task">
    </form>
</body>
</html>
