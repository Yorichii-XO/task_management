<?php
// Include the database connection script
require_once './connexion.php';

// Fetch all users from the database
$stmt = $con->query("SELECT * FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tasks</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="">
<?php include './pages/header.php'; ?>

<div class="flex">
 
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
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['ID_user'] ?>"><?= $user['username'] ?></option>
                <?php endforeach; ?>
            </select>
            <button id="showTasksBtn" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Show Tasks</button>
        </div>

        <!-- Task Table -->
        <div id="taskTable"></div>

    </div>
</div>

<script>
$(document).ready(function() {
    // Show tasks button click event
    $('#showTasksBtn').click(function() {
        // Get selected user ID
        var userId = $('#userSelect').val();

        // Fetch tasks for the selected user
        $.ajax({
            type: 'GET',
            url: 'fetch_Tasks.php',
            data: { ID_user: userId },
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
