<?php
// Include the database connection script
include './connexion.php';

// Check if task ID is provided
if (isset($_GET['task_id'])) {
    $taskId = $_GET['task_id'];

    // Fetch task details from the database
    $stmt = $con->prepare("SELECT * FROM tasks WHERE task_id = :task_id");
    $stmt->execute([':task_id' => $taskId]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-200">
<?php include './pages/header.php'; ?>

<div class="container mx-auto p-8">
    <h2 class="text-2xl font-bold mb-4">Edit Task</h2>

    <?php if ($task): ?>
        <form id="editTaskForm" method="POST" action="update_task.php">
            <input type="hidden" name="task_id" value="<?= htmlspecialchars($task['task_id']) ?>">

            <div class="mb-4">
                <label for="task_title" class="block text-gray-700 font-bold mb-2">Task Title:</label>
                <input type="text" id="task_title" name="task_title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?= htmlspecialchars($task['task_title']) ?>">
            </div>

            <div class="mb-4">
                <label for="task_description" class="block text-gray-700 font-bold mb-2">Description:</label>
                <textarea id="task_description" name="task_description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?= htmlspecialchars($task['task_description']) ?></textarea>
            </div>

            <div class="mb-4">
                <label for="priority" class="block text-gray-700 font-bold mb-2">Priority:</label>
                <input type="text" id="priority" name="priority" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?= htmlspecialchars($task['priority']) ?>">
            </div>

            <div class="mb-4">
                <label for="due_date" class="block text-gray-700 font-bold mb-2">Due Date:</label>
                <input type="date" id="due_date" name="due_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?= htmlspecialchars($task['due_date']) ?>">
            </div>

            <div class="mb-4">
                <label for="is_completed" class="block text-gray-700 font-bold mb-2">Completed:</label>
                <input type="checkbox" id="is_completed" name="is_completed" <?= $task['is_completed'] ? 'checked' : '' ?>>
            </div>

            <div class="mb-4">
                <label for="ID_category" class="block text-gray-700 font-bold mb-2">Category ID:</label>
                <input type="text" id="ID_category" name="ID_category" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?= htmlspecialchars($task['ID_category']) ?>">
            </div>

            <div class="mb-4">
                <label for="ID_user" class="block text-gray-700 font-bold mb-2">User ID:</label>
                <input type="text" id="ID_user" name="ID_user" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?= htmlspecialchars($task['ID_user']) ?>">
            </div>

            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    Update Task
                </button>
            </div>
        </form>
    <?php else: ?>
        <p>Task not found.</p>
    <?php endif; ?>
</div>

</body>
</html>
