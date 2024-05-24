<?php
require_once '../connexion.php'; // Include your database connection file here
require_once '../vendor/autoload.php'; // Autoload Dompdf classes

session_start();
use Dompdf\Dompdf;
use Dompdf\Options;

if (!isset($_SESSION['username']) || $_SESSION['role_type'] != 'user' || !isset($_SESSION['ID_user'])) {
    header("Location: login.php");
    exit();
}

// Function to import tasks from JSON file into the database
function importTasksFromJSON($con, $json_data) {
    // Decode JSON data
    $tasks = json_decode($json_data, true);

    // Insert tasks into database
    foreach ($tasks as $task) {
        $ID_user = $_SESSION['ID_user']; // Get the ID of the current user from the session
        $task_title = $task['task_title'];
        $task_description = $task['task_description'];
        $priority = $task['priority'];
        $due_date = $task['due_date'];
        $is_completed = $task['is_completed'];
        $ID_category = $task['ID_category'];

        // SQL query to insert task into database
        $sql = "INSERT INTO tasks (ID_user, task_title, task_description, priority, due_date, is_completed, ID_category) 
                VALUES (:ID_user, :task_title, :task_description, :priority, :due_date, :is_completed, :ID_category)";

        try {
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':ID_user', $ID_user);
            $stmt->bindParam(':task_title', $task_title);
            $stmt->bindParam(':task_description', $task_description);
            $stmt->bindParam(':priority', $priority);
            $stmt->bindParam(':due_date', $due_date);
            $stmt->bindParam(':is_completed', $is_completed);
            $stmt->bindParam(':ID_category', $ID_category);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "<p>Error inserting task: " . $e->getMessage() . "</p>";
        }
    }
}

// Function to delete a task
function deleteTask($con, $task_id) {
    $sql = "DELETE FROM tasks WHERE task_id = :task_id";

    try {
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':task_id', $task_id);
        $stmt->execute();
    } catch (PDOException $e) {
        echo "<p>Error deleting task: " . $e->getMessage() . "</p>";
    }
}

// Function to edit a task
function editTask($con, $task) {
    $sql = "UPDATE tasks SET task_title = :task_title, task_description = :task_description, priority = :priority, due_date = :due_date, is_completed = :is_completed, ID_category = :ID_category WHERE task_id = :task_id";

    try {
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':task_title', $task['task_title']);
        $stmt->bindParam(':task_description', $task['task_description']);
        $stmt->bindParam(':priority', $task['priority']);
        $stmt->bindParam(':due_date', $task['due_date']);
        $stmt->bindParam(':is_completed', $task['is_completed']);
        $stmt->bindParam(':ID_category', $task['ID_category']);
        $stmt->bindParam(':task_id', $task['task_id']);
        $stmt->execute();
    } catch (PDOException $e) {
        echo "<p>Error updating task: " . $e->getMessage() . "</p>";
    }
}

// Check if delete action is requested
if (isset($_GET['delete'])) {
    $task_id = $_GET['delete'];
    deleteTask($con, $task_id);
}

// Check if edit action is requested
if (isset($_POST['edit'])) {
    $task = [
        'task_id' => $_POST['task_id'],
        'task_title' => $_POST['task_title'],
        'task_description' => $_POST['task_description'],
        'priority' => $_POST['priority'],
        'due_date' => $_POST['due_date'],
        'is_completed' => isset($_POST['is_completed']) ? 1 : 0,
        'ID_category' => $_POST['ID_category']
    ];
    editTask($con, $task);
}

// Check if add action is requested
if (isset($_POST['add'])) {
    $task = [
        'task_title' => $_POST['task_title'],
        'task_description' => $_POST['task_description'],
        'priority' => $_POST['priority'],
        'due_date' => $_POST['due_date'],
        'is_completed' => isset($_POST['is_completed']) ? 1 : 0,
        'ID_category' => $_POST['ID_category']
    ];
    // Insert the new task into the database
    importTasksFromJSON($con, json_encode([$task])); // Reusing the import function for single task insertion
}

// Check if the form is submitted and a file is uploaded
if (isset($_POST['import']) && isset($_FILES['tasks_json']) && $_FILES['tasks_json']['error'] === UPLOAD_ERR_OK) {
    $file_tmp = $_FILES['tasks_json']['tmp_name'];
    $json_data = file_get_contents($file_tmp);

    // Import tasks from JSON file
    importTasksFromJSON($con, $json_data);
}

// Retrieve all tasks from the database
$sql = "SELECT tasks.*, users.username FROM tasks JOIN users ON tasks.ID_user = users.ID_user";
try {
    $stmt = $con->prepare($sql);
    $stmt->execute();
} catch (PDOException $e) {
    echo "<p>Error retrieving tasks: " . $e->getMessage() . "</p>";
}

// Retrieve all categories from the database
$sql_categories = "SELECT * FROM categories";
$stmt_categories = $con->query($sql_categories);
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);
// Handle PDF export
if (isset($_POST['export_pdf'])) {
    $stmt->execute(); // Re-execute the query to fetch data again
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Create new PDF instance and set options
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);

    // Generate HTML content for the PDF
    $html = '<h1>Tasks</h1>';
    $html .= '<table border="1" cellpadding="5" cellspacing="0">';
    $html .= '<thead><tr>';
    $html .= '<th>ID</th>';
    $html .= '<th>Title</th>';
    $html .= '<th>Description</th>';
    $html .= '<th>Priority</th>';
    $html .= '<th>Due Date</th>';
    $html .= '<th>Completed</th>';
    $html .= '<th>Category</th>';
    $html .= '</tr></thead>';
    $html .= '<tbody>';

    foreach ($tasks as $task) {
        $html .= '<tr>';
        $html .= '<td>' . $task['task_id'] . '</td>';
        $html .= '<td>' . $task['task_title'] . '</td>';
        $html .= '<td>' . $task['task_description'] . '</td>';
        $html .= '<td>' . $task['priority'] . '</td>';
        $html .= '<td>' . $task['due_date'] . '</td>';
        $html .= '<td>' . ($task['is_completed'] ? 'Yes' : 'No') . '</td>';
        $html .= '<td>' . $task['ID_category'] . '</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';

    // Load HTML content into Dompdf
    $dompdf->loadHtml($html);

    // Render the PDF
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();

    // Output the generated PDF to Browser for download
    $dompdf->stream('tasks.pdf', ['Attachment' => 1]);
    exit();
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
        
        <!-- User Selection -->
        <div class="mb-4">
            <label for="userSelect" class="block text-sm font-medium text-gray-700">Select User:</label>
            <select id="userSelect" name="userSelect" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                <!-- Options will be populated dynamically using JavaScript -->
            </select>
        </div>
        
        <!-- Button to Show Tasks -->
        <button id="showTasksBtn" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Show Tasks</button>
        
        <!-- Task Table -->
        <div id="taskTableContainer"></div>

    </div>
</div>

<script>
$(document).ready(function() {
    // Populate user select options
    $.ajax({
        type: 'GET',
        url: 'fetch_users.php',
        success: function(users) {
            // Populate user select options
            users.forEach(function(user) {
                $('#userSelect').append(`<option value="${user.ID_user}">${user.username}</option>`);
            });
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
            // Handle error
        }
    });

    // Fetch and display tasks when user is selected
    $('#showTasksBtn').click(function() {
        const userId = $('#userSelect').val();
        $.ajax({
            type: 'GET',
            url: 'fetch_tasks.php',
            data: { user_id: userId },
            success: function(tasksHtml) {
                $('#taskTableContainer').html(tasksHtml);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                // Handle error
            }
        });
    });
});
</script>

</body>
</html>
