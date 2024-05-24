<?php
// Include the database connection script
include './connexion.php';

// Check if user ID is provided
if (isset($_GET['user_id'])) {
    $userId = $_GET['user_id'];

    // Fetch tasks for the selected user
    $stmt = $con->prepare("SELECT * FROM tasks WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $userId]);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Display tasks in a table format
    if (!empty($tasks)) {
        echo "<table class='w-full'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th class='px-4 py-2 border border-gray-300'>Task ID</th>";
        echo "<th class='px-4 py-2 border border-gray-300'>Task Name</th>";
        echo "<th class='px-4 py-2 border border-gray-300'>Task Description</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        foreach ($tasks as $task) {
            echo "<tr>";
            echo "<td class='px-4 py-2 border border-gray-300'>" . $task['task_id'] . "</td>";
            echo "<td class='px-4 py-2 border border-gray-300'>" . $task['task_name'] . "</td>";
            echo "<td class='px-4 py-2 border border-gray-300'>" . $task['task_description'] . "</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<p>No tasks found for this user.</p>";
    }
} else {
    echo "<p>No user ID provided.</p>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tasks</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-200">

<div class="flex">
    <!-- Sidebar -->
    <div class="bg-gray-800 text-white h-screen w-1/5">
        <div class="p-4">
            <ul class="mt-4">
                <li class="mb-2"><a href="dashboard.php" class="block px-4 py-2 hover:bg-gray-700">Dashboard</a></li>
                <li class="mb-2"><a href="users.php" class="block px-4 py-2 hover:bg-gray-700">Users</a></li>
                <li class="mb-2"><a href="tasks.php" class="block px-4 py-2 hover:bg-gray-700">Tasks</a></li>
                <li><a href="logout.php" class="block px-4 py-2 hover:bg-gray-700">Logout</a></li>
            </ul>
        </div>
    </div>

    <!-- Content -->
    <div class="w-4/5 p-8">
        <!-- Header -->
        <header class="bg-gray-800 text-white mb-8">
            <div class="container mx-auto px-6 py-3 flex justify-between items-center">
                <!-- Logo -->
                <div>
                    <h1 class="text-xl font-bold">Manage Tasks</h1>
                </div>
            </div>
        </header>

        <!-- Main Content Here -->
        <h2 class="text-2xl font-bold mb-4">Task Management</h2>
        
        <!-- User Selection Dropdown and Button -->
        <div class="flex mb-4">
            <select id="userSelect" class="mr-4">
                <!-- Users will be dynamically populated here -->
            </select>
            <button id="showTasksBtn" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Show Tasks</button>
        </div>

        <!-- Task Table -->
        <div id="taskTable"></div>

    </div>
</div>

<script>
$(document).ready(function() {
    // Fetch users and populate dropdown
    $.ajax({
        type: 'GET',
        url: 'fetch_users.php',
        success: function(users) {
            // Add users to the dropdown
            users.forEach(function(user) {
                $('#userSelect').append(`<option value="${user.ID_user}">${user.username}</option>`);
            });
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });

    // Show tasks button click event
    $('#showTasksBtn').click(function() {
        // Get selected user ID
        var userId = $('#userSelect').val();

        // Fetch tasks for the selected user
        $.ajax({
            type: 'GET',
            url: 'fetch_tasks.php',
            data: { user_id: userId },
            success: function(html) {
                // Display tasks in the task table
                $('#taskTable').html(html);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });
});
</script>

</body>
</html>
