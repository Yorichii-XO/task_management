<?php require_once '../connexion.php'; // Include your database connection file here
require_once '../vendor/autoload.php'; // Autoload Dompdf classes

session_start();

use Dompdf\Dompdf;
use Dompdf\Options;


if (!isset($_SESSION['username']) || $_SESSION['role_type'] != 'user') {
    header("Location: login.php");
    exit();
}

// Function to import tasks from JSON file into the database
function importTasksFromJSON($con, $json_data) {
    // Decode JSON data
    $tasks = json_decode($json_data, true);

    // Insert tasks into database
    foreach ($tasks as $task) {
        $task_title = $task['task_title'];
        $task_description = $task['task_description'];
        $priority = $task['priority'];
        $due_date = $task['due_date'];
        $is_completed = $task['is_completed'];
        $ID_category = $task['ID_category'];

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
}// Check if user ID is set in session
if (!isset($_SESSION['ID_user'])) {
    // Redirect or handle the case where the user ID is not set
    // For example:
    echo "User ID not found.";
    exit();
}

// Retrieve user ID from session
$user_id = $_SESSION['ID_user'];

// Retrieve all tasks for the current user from the database
$sql = "SELECT * FROM tasks WHERE ID_user = :user_id";
$stmt = $con->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();

// Retrieve all categories from the database
$sql_categories = "SELECT * FROM categories";
$stmt_categories = $con->query($sql_categories);
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);

// Handle PDF export
if (isset($_POST['export_pdf'])) {
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
    <title>User Page</title>
    <link href="tasks.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php include '../pages/header.php'; ?>
    <div class="index">
   
        <div class="flex">
            <form id="importForm" enctype="multipart/form-data">
                <input class="text-sm font-bold mb-4" type="file" name="tasks_json" accept=".json" required>
                <div class="voltage-button flex  text-black">
                    <button style="color: black;" name="import" type="submit" class="px-4 py-2 ">Import Tasks</button>
                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 234.6 61.3" preserveAspectRatio="none" xml:space="preserve">

                        <filter id="glow">
                            <feGaussianBlur class="blur" result="coloredBlur" stdDeviation="2"></feGaussianBlur>
                            <feTurbulence type="fractalNoise" baseFrequency="0.075" numOctaves="0.3" result="turbulence"></feTurbulence>
                            <feDisplacementMap in="SourceGraphic" in2="turbulence" scale="30" xChannelSelector="R" yChannelSelector="G" result="displace"></feDisplacementMap>
                            <feMerge>
                                <feMergeNode in="coloredBlur"></feMergeNode>
                                <feMergeNode in="coloredBlur"></feMergeNode>
                                <feMergeNode in="coloredBlur"></feMergeNode>
                                <feMergeNode in="displace"></feMergeNode>
                                <feMergeNode in="SourceGraphic"></feMergeNode>
                            </feMerge>
                        </filter>
                        <path class="voltage line-1" d="m216.3 51.2c-3.7 0-3.7-1.1-7.3-1.1-3.7 0-3.7 6.8-7.3 6.8-3.7 0-3.7-4.6-7.3-4.6-3.7 0-3.7 3.6-7.3 3.6-3.7 0-3.7-0.9-7.3-0.9-3.7 0-3.7-2.7-7.3-2.7-3.7 0-3.7 7.8-7.3 7.8-3.7 0-3.7-4.9-7.3-4.9-3.7 0-3.7-7.8-7.3-7.8-3.7 0-3.7-1.1-7.3-1.1-3.7 0-3.7 3.1-7.3 3.1-3.7 0-3.7 10.9-7.3 10.9-3.7 0-3.7-12.5-7.3-12.5-3.7 0-3.7 4.6-7.3 4.6-3.7 0-3.7 4.5-7.3 4.5-3.7 0-3.7 3.6-7.3 3.6-3.7 0-3.7-10-7.3-10-3.7 0-3.7-0.4-7.3-0.4-3.7 0-3.7 2.3-7.3 2.3-3.7 0-3.7 7.1-7.3 7.1-3.7 0-3.7-11.2-7.3-11.2-3.7 0-3.7 3.5-7.3 3.5-3.7 0-3.7 3.6-7.3 3.6-3.7 0-3.7-2.9-7.3-2.9-3.7 0-3.7 8.4-7.3 8.4-3.7 0-3.7-14.6-7.3-14.6-3.7 0-3.7 5.8-7.3 5.8-2.2 0-3.8-0.4-5.5-1.5-1.8-1.1-1.8-2.9-2.9-4.8-1-1.8 1.9-2.7 1.9-4.8 0-3.4-2.1-3.4-2.1-6.8s-9.9-3.4-9.9-6.8 8-3.4 8-6.8c0-2.2 2.1-2.4 3.1-4.2 1.1-1.8 0.2-3.9 2-5 1.8-1 3.1-7.9 5.3-7.9 3.7 0 3.7 0.9 7.3 0.9 3.7 0 3.7 6.7 7.3 6.7 3.7 0 3.7-1.8 7.3-1.8 3.7 0 3.7-0.6 7.3-0.6 3.7 0 3.7-7.8 7.3-7.8h7.3c3.7 0 3.7 4.7 7.3 4.7 3.7 0 3.7-1.1 7.3-1.1 3.7 0 3.7 11.6 7.3 11.6 3.7 0 3.7-2.6 7.3-2.6 3.7 0 3.7-12.9 7.3-12.9 3.7 0 3.7 10.9 7.3 10.9 3.7 0 3.7 1.3 7.3 1.3 3.7 0 3.7-8.7 7.3-8.7 3.7 0 3.7 11.5 7.3 11.5 3.7 0 3.7-1.4 7.3-1.4 3.7 0 3.7-2.6 7.3-2.6 3.7 0 3.7-5.8 7.3-5.8 3.7 0 3.7-1.3 7.3-1.3 3.7 0 3.7 6.6 7.3 6.6s3.7-9.3 7.3-9.3c3.7 0 3.7 0.2 7.3 0.2 3.7 0 3.7 8.5 7.3 8.5 3.7 0 3.7 0.2 7.3 0.2 3.7 0 3.7-1.5 7.3-1.5 3.7 0 3.7 1.6 7.3 1.6s3.7-5.1 7.3-5.1c2.2 0 0.6 9.6 2.4 10.7s4.1-2 5.1-0.1c1 1.8 10.3 2.2 10.3 4.3 0 3.4-10.7 3.4-10.7 6.8s1.2 3.4 1.2 6.8 1.9 3.4 1.9 6.8c0 2.2 7.2 7.7 6.2 9.5-1.1 1.8-12.3-6.5-14.1-5.5-1.7 0.9-0.1 6.2-2.2 6.2z" fill="transparent" stroke="#fff"></path>
                        <path class="voltage line-2" d="m216.3 52.1c-3 0-3-0.5-6-0.5s-3 3-6 3-3-2-6-2-3 1.6-6 1.6-3-0.4-6-0.4-3-1.2-6-1.2-3 3.4-6 3.4-3-2.2-6-2.2-3-3.4-6-3.4-3-0.5-6-0.5-3 1.4-6 1.4-3 4.8-6 4.8-3-5.5-6-5.5-3 2-6 2-3 2-6 2-3 1.6-6 1.6-3-4.4-6-4.4-3-0.2-6-0.2-3 1-6 1-3 3.1-6 3.1-3-4.9-6-4.9-3 1.5-6 1.5-3 1.6-6 1.6-3-1.3-6-1.3-3 3.7-6 3.7-3-6.4-6-6.4-3 2.5-6 2.5h-6c-3 0-3-0.6-6-0.6s-3-1.4-6-1.4-3 0.9-6 0.9-3 4.3-6 4.3-3-3.5-6-3.5c-2.2 0-3.4-1.3-5.2-2.3-1.8-1.1-3.6-1.5-4.6-3.3s-4.4-3.5-4.4-5.7c0-3.4 0.4-3.4 0.4-6.8s2.9-3.4 2.9-6.8-0.8-3.4-0.8-6.8c0-2.2 0.3-4.2 1.3-5.9 1.1-1.8 0.8-6.2 2.6-7.3 1.8-1 5.5-2 7.7-2 3 0 3 2 6 2s3-0.5 6-0.5 3 5.1 6 5.1 3-1.1 6-1.1 3-5.6 6-5.6 3 4.8 6 4.8 3 0.6 6 0.6 3-3.8 6-3.8 3 5.1 6 5.1 3-0.6 6-0.6 3-1.2 6-1.2 3-2.6 6-2.6 3-0.6 6-0.6 3 2.9 6 2.9 3-4.1 6-4.1 3 0.1 6 0.1 3 3.7 6 3.7 3 0.1 6 0.1 3-0.6 6-0.6 3 0.7 6 0.7 3-2.2 6-2.2 3 4.4 6 4.4 3-1.7 6-1.7 3-4 6-4 3 4.7 6 4.7 3-0.5 6-0.5 3-0.8 6-0.8 3-3.8 6-3.8 3 6.3 6 6.3 3-4.8 6-4.8 3 1.9 6 1.9 3-1.9 6-1.9 3 1.3 6 1.3c2.2 0 5-0.5 6.7 0.5 1.8 1.1 2.4 4 3.5 5.8 1 1.8 0.3 3.7 0.3 5.9 0 3.4 3.4 3.4 3.4 6.8s-3.3 3.4-3.3 6.8 4 3.4 4 6.8c0 2.2-6 2.7-7 4.4-1.1 1.8 1.1 6.7-0.7 7.7-1.6 0.8-4.7-1.1-6.8-1.1z" fill="transparent" stroke="#fff"></path>
                    </svg>
                    <div class="dots">
                        <div class="dot dot-1"></div>
                        <div class="dot dot-2"></div>
                        <div class="dot dot-3"></div>
                        <div class="dot dot-4"></div>
                        <div class="dot dot-5"></div>
                    </div>
                </div>
            </form>

            <form action="" method="post">
                <div class="voltage-button flex ">
                    <button style="color: black;" type="submit" name="export_pdf" class=" px-4 py-2 ">Export to PDF</button>
                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 234.6 61.3" preserveAspectRatio="none" xml:space="preserve">

                        <filter id="glow">
                            <feGaussianBlur class="blur" result="coloredBlur" stdDeviation="2"></feGaussianBlur>
                            <feTurbulence type="fractalNoise" baseFrequency="0.075" numOctaves="0.3" result="turbulence"></feTurbulence>
                            <feDisplacementMap in="SourceGraphic" in2="turbulence" scale="30" xChannelSelector="R" yChannelSelector="G" result="displace"></feDisplacementMap>
                            <feMerge>
                                <feMergeNode in="coloredBlur"></feMergeNode>
                                <feMergeNode in="coloredBlur"></feMergeNode>
                                <feMergeNode in="coloredBlur"></feMergeNode>
                                <feMergeNode in="displace"></feMergeNode>
                                <feMergeNode in="SourceGraphic"></feMergeNode>
                            </feMerge>
                        </filter>
                        <path class="voltage line-1" d="m216.3 51.2c-3.7 0-3.7-1.1-7.3-1.1-3.7 0-3.7 6.8-7.3 6.8-3.7 0-3.7-4.6-7.3-4.6-3.7 0-3.7 3.6-7.3 3.6-3.7 0-3.7-0.9-7.3-0.9-3.7 0-3.7-2.7-7.3-2.7-3.7 0-3.7 7.8-7.3 7.8-3.7 0-3.7-4.9-7.3-4.9-3.7 0-3.7-7.8-7.3-7.8-3.7 0-3.7-1.1-7.3-1.1-3.7 0-3.7 3.1-7.3 3.1-3.7 0-3.7 10.9-7.3 10.9-3.7 0-3.7-12.5-7.3-12.5-3.7 0-3.7 4.6-7.3 4.6-3.7 0-3.7 4.5-7.3 4.5-3.7 0-3.7 3.6-7.3 3.6-3.7 0-3.7-10-7.3-10-3.7 0-3.7-0.4-7.3-0.4-3.7 0-3.7 2.3-7.3 2.3-3.7 0-3.7 7.1-7.3 7.1-3.7 0-3.7-11.2-7.3-11.2-3.7 0-3.7 3.5-7.3 3.5-3.7 0-3.7 3.6-7.3 3.6-3.7 0-3.7-2.9-7.3-2.9-3.7 0-3.7 8.4-7.3 8.4-3.7 0-3.7-14.6-7.3-14.6-3.7 0-3.7 5.8-7.3 5.8-2.2 0-3.8-0.4-5.5-1.5-1.8-1.1-1.8-2.9-2.9-4.8-1-1.8 1.9-2.7 1.9-4.8 0-3.4-2.1-3.4-2.1-6.8s-9.9-3.4-9.9-6.8 8-3.4 8-6.8c0-2.2 2.1-2.4 3.1-4.2 1.1-1.8 0.2-3.9 2-5 1.8-1 3.1-7.9 5.3-7.9 3.7 0 3.7 0.9 7.3 0.9 3.7 0 3.7 6.7 7.3 6.7 3.7 0 3.7-1.8 7.3-1.8 3.7 0 3.7-0.6 7.3-0.6 3.7 0 3.7-7.8 7.3-7.8h7.3c3.7 0 3.7 4.7 7.3 4.7 3.7 0 3.7-1.1 7.3-1.1 3.7 0 3.7 11.6 7.3 11.6 3.7 0 3.7-2.6 7.3-2.6 3.7 0 3.7-12.9 7.3-12.9 3.7 0 3.7 10.9 7.3 10.9 3.7 0 3.7 1.3 7.3 1.3 3.7 0 3.7-8.7 7.3-8.7 3.7 0 3.7 11.5 7.3 11.5 3.7 0 3.7-1.4 7.3-1.4 3.7 0 3.7-2.6 7.3-2.6 3.7 0 3.7-5.8 7.3-5.8 3.7 0 3.7-1.3 7.3-1.3 3.7 0 3.7 6.6 7.3 6.6s3.7-9.3 7.3-9.3c3.7 0 3.7 0.2 7.3 0.2 3.7 0 3.7 8.5 7.3 8.5 3.7 0 3.7 0.2 7.3 0.2 3.7 0 3.7-1.5 7.3-1.5 3.7 0 3.7 1.6 7.3 1.6s3.7-5.1 7.3-5.1c2.2 0 0.6 9.6 2.4 10.7s4.1-2 5.1-0.1c1 1.8 10.3 2.2 10.3 4.3 0 3.4-10.7 3.4-10.7 6.8s1.2 3.4 1.2 6.8 1.9 3.4 1.9 6.8c0 2.2 7.2 7.7 6.2 9.5-1.1 1.8-12.3-6.5-14.1-5.5-1.7 0.9-0.1 6.2-2.2 6.2z" fill="transparent" stroke="#fff"></path>
                        <path class="voltage line-2" d="m216.3 52.1c-3 0-3-0.5-6-0.5s-3 3-6 3-3-2-6-2-3 1.6-6 1.6-3-0.4-6-0.4-3-1.2-6-1.2-3 3.4-6 3.4-3-2.2-6-2.2-3-3.4-6-3.4-3-0.5-6-0.5-3 1.4-6 1.4-3 4.8-6 4.8-3-5.5-6-5.5-3 2-6 2-3 2-6 2-3 1.6-6 1.6-3-4.4-6-4.4-3-0.2-6-0.2-3 1-6 1-3 3.1-6 3.1-3-4.9-6-4.9-3 1.5-6 1.5-3 1.6-6 1.6-3-1.3-6-1.3-3 3.7-6 3.7-3-6.4-6-6.4-3 2.5-6 2.5h-6c-3 0-3-0.6-6-0.6s-3-1.4-6-1.4-3 0.9-6 0.9-3 4.3-6 4.3-3-3.5-6-3.5c-2.2 0-3.4-1.3-5.2-2.3-1.8-1.1-3.6-1.5-4.6-3.3s-4.4-3.5-4.4-5.7c0-3.4 0.4-3.4 0.4-6.8s2.9-3.4 2.9-6.8-0.8-3.4-0.8-6.8c0-2.2 0.3-4.2 1.3-5.9 1.1-1.8 0.8-6.2 2.6-7.3 1.8-1 5.5-2 7.7-2 3 0 3 2 6 2s3-0.5 6-0.5 3 5.1 6 5.1 3-1.1 6-1.1 3-5.6 6-5.6 3 4.8 6 4.8 3 0.6 6 0.6 3-3.8 6-3.8 3 5.1 6 5.1 3-0.6 6-0.6 3-1.2 6-1.2 3-2.6 6-2.6 3-0.6 6-0.6 3 2.9 6 2.9 3-4.1 6-4.1 3 0.1 6 0.1 3 3.7 6 3.7 3 0.1 6 0.1 3-0.6 6-0.6 3 0.7 6 0.7 3-2.2 6-2.2 3 4.4 6 4.4 3-1.7 6-1.7 3-4 6-4 3 4.7 6 4.7 3-0.5 6-0.5 3-0.8 6-0.8 3-3.8 6-3.8 3 6.3 6 6.3 3-4.8 6-4.8 3 1.9 6 1.9 3-1.9 6-1.9 3 1.3 6 1.3c2.2 0 5-0.5 6.7 0.5 1.8 1.1 2.4 4 3.5 5.8 1 1.8 0.3 3.7 0.3 5.9 0 3.4 3.4 3.4 3.4 6.8s-3.3 3.4-3.3 6.8 4 3.4 4 6.8c0 2.2-6 2.7-7 4.4-1.1 1.8 1.1 6.7-0.7 7.7-1.6 0.8-4.7-1.1-6.8-1.1z" fill="transparent" stroke="#fff"></path>
                    </svg>
                    <div class="dots">
                        <div class="dot dot-1"></div>
                        <div class="dot dot-2"></div>
                        <div class="dot dot-3"></div>
                        <div class="dot dot-4"></div>
                        <div class="dot dot-5"></div>
                    </div>
                </div>
            </form>
        </div>
        <div class="fixed top-0 left-0 w-full h-full flex justify-center items-center hidden" id="overlay" style="background-color: rgba(0, 0, 0, 0.5);">
            <div class="form-container bg-white rounded-lg shadow-md p-8 w-1/2">
                <h2>Add Task</h2>
                <form action="" method="post" id="addTaskForm">
                    <label class="block mb-2" for="add_task_title">Title:</label>
                    <input type="text" name="task_title" id="add_task_title" required><br>
                    <label class="block mb-2" for="add_task_description">Description:</label>
                    <input type="text" name="task_description" id="add_task_description" required><br>
                    <label class="block mb-2" for="add_priority">Priority:</label>
                    <select name="priority" id="add_priority" required>
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                    </select><br>
                    <label for="add_due_date">Due Date:</label>
                    <input type="date" name="due_date" id="add_due_date" required><br>
                    <label for="add_is_completed">Completed:</label>
                    <input type="checkbox" name="is_completed" id="add_is_completed"><br>
                    <label for="add_ID_category">Category:</label>
                    <select name="ID_category" id="add_ID_category" required>
                        <?php foreach ($categories as $category) : ?>
                            <option value="<?php echo htmlspecialchars($category['ID_category']); ?>"><?php echo htmlspecialchars($category['category_name']); ?></option>
                        <?php endforeach; ?>
                    </select><br>
                    <input type="submit" name="add" value="Add Task">
                </form>
            </div>
        </div>
        <div class="text-right mb-4">
            <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600" onclick="showAddForm()">Add Task</button>
        </div>
        <h2 class="h2 text-2xl font-bold mb-4">All Task</h2>

        <!-- Categories Table -->
        <div class="table bg-white p-4 rounded-lg shadow-md">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b border-gray-200">ID</th>
                        <th class="py-2 px-4 border-b border-gray-200">Title</th>
                        <th class="py-2 px-4 border-b border-gray-200">Description</th>
                        <th class="py-2 px-4 border-b border-gray-200">Priority</th>
                        <th class="py-2 px-4 border-b border-gray-200">Due Date</th>
                        <th class="py-2 px-4 border-b border-gray-200">Completed</th>
                        <th class="py-2 px-4 border-b border-gray-200">Category</th>
                        <th class="py-2 px-4 border-b border-gray-200">User ID</th> <!-- Added column for User ID -->
                        <th class="py-2 px-4 border-b border-gray-200">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) : ?>
                        <tr>
                            <td class='py-2 px-4 border-b border-gray-200'><?php echo htmlspecialchars($row['task_id']); ?></td>
                            <td class='py-2 px-4 border-b border-gray-200'><?php echo htmlspecialchars($row['task_title']); ?></td>
                            <td class='py-2 px-4 border-b border-gray-200'><?php echo htmlspecialchars($row['task_description']); ?></td>
                            <td class='py-2 px-4 border-b border-gray-200'><?php echo htmlspecialchars($row['priority']); ?></td>
                            <td class='py-2 px-4 border-b border-gray-200'><?php echo htmlspecialchars($row['due_date']); ?></td>
                            <td class='py-2 px-4 border-b border-gray-200'><?php echo $row['is_completed'] ? 'Yes' : 'No'; ?></td>
                            <td class='py-2 px-4 border-b border-gray-200'><?php echo htmlspecialchars($row['ID_category']); ?></td>
                            <td class='py-2 px-4 border-b border-gray-200'><?php echo htmlspecialchars($row['ID_user']); ?></td> <!-- Display User ID -->

                            <td class='py-2 px-4 border-b border-gray-200'>
                                <a href="?delete=<?php echo $row['task_id']; ?>">Delete</a>
                                <button onclick="showEditForm(<?php echo htmlspecialchars(json_encode($row)); ?>)">Edit</button>
                                <button onclick="showDetails(<?php echo htmlspecialchars(json_encode($row)); ?>)">Details</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Edit Form -->
            <div id="editForm" style="display:none;">
                <h2>Edit Task</h2>
                <form action="" method="post">
                    <input type="hidden" name="task_id" id="edit_task_id">
                    <label for="edit_task_title">Title:</label>
                    <input type="text" name="task_title" id="edit_task_title" required><br>
                    <label for="edit_task_description">Description:</label>
                    <input type="text" name="task_description" id="edit_task_description" required><br>
                    <label for="edit_priority">Priority:</label>
                    <select name="priority" id="edit_priority" required>
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                    </select><br>
                    <label for="edit_due_date">Due Date:</label>
                    <input type="date" name="due_date" id="edit_due_date" required><br>
                    <label for="edit_is_completed">Completed:</label>
                    <input type="checkbox" name="is_completed" id="edit_is_completed"><br>
                    <label for="edit_ID_category">Category:</label>
                    <select name="ID_category" id="edit_ID_category" required>
                        <?php foreach ($categories as $category) : ?>
                            <option value="<?php echo htmlspecialchars($category['ID_category']); ?>"><?php echo htmlspecialchars($category['category_name']); ?></option>
                        <?php endforeach; ?>
                    </select><br>
                    <input type="submit" name="edit" value="Save Changes">
                </form>
            </div>

            <!-- Task Details -->
            <div id="taskDetails" style="display:none;">
                <h2>Task Details</h2>
                <p id="details_task_id"></p>
                <p id="details_task_title"></p>
                <p id="details_task_description"></p>
                <p id="details_priority"></p>
                <p id="details_due_date"></p>
                <p id="details_is_completed"></p>
                <p id="details_ID_category"></p>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            // Function to show add form
            function showAddForm() {
                $('#overlay').removeClass('hidden');
            }

            // Function to hide add form
            function hideAddForm() {
                $('#overlay').addClass('hidden');
            }

            // AJAX request to add task
            $(document).ready(function() {
                $('#addTaskForm').submit(function(e) {
                    e.preventDefault();
                    $.ajax({
                        type: 'POST',
                        url: 'addTask.php', // Change to your PHP script to handle adding task
                        data: $('#addTaskForm').serialize(),
                        success: function(response) {
                            // Handle success, for example, hide the form and reload tasks list
                            hideAddForm();
                            location.reload();
                        },
                        error: function(xhr, status, error) {
                            // Handle error, for example, display an alert
                            alert('Error adding task: ' + error);
                        }
                    });
                });
            });
            $('#importForm').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    type: 'POST',
                    url: 'importTasks.php',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        var result = JSON.parse(response);
                        if (result.status == 'success') {
                            loadTasks(); // Refresh the task list
                        } else {
                            alert('Error importing tasks: ' + result.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Error importing tasks: ' + error);
                    }
                });
            });

            // Function to show edit form
            window.showEditForm = function(task) {
                $('#edit_task_id').val(task.task_id);
                $('#edit_task_title').val(task.task_title);
                $('#edit_task_description').val(task.task_description);
                $('#edit_priority').val(task.priority);
                $('#edit_due_date').val(task.due_date);
                $('#edit_is_completed').prop('checked', task.is_completed);
                $('#edit_ID_category').val(task.ID_category);
                $('#editOverlay').removeClass('hidden');
            }

            // Function to hide edit form
            window.hideEditForm = function() {
                $('#editOverlay').addClass('hidden');
            }
            $(document).ready(function() {
                $('#addTaskForm').submit(function(e) {
                    e.preventDefault();
                    $.ajax({
                        type: 'POST',
                        url: 'addTask.php', // Change to your PHP script to handle adding task
                        data: $('#addTaskForm').serialize(),
                        success: function(response) {
                            // Handle success, for example, hide the form and reload tasks list
                            hideAddForm();
                            location.reload();
                        },
                        error: function(xhr, status, error) {
                            // Handle error, for example, display an alert
                            alert('Error adding task: ' + error);
                        }
                    });
                });
            });

            function showEditForm(task) {
                // AJAX request to fetch edit form data
                $.ajax({
                    type: 'POST',
                    url: 'editTask.php', // Replace with the actual URL of your PHP script
                    data: {
                        task_id: task.task_id
                    }, // Send task ID to fetch corresponding edit form data
                    success: function(response) {
                        // Display edit form data as alert
                        alert(response);
                    },
                    error: function(xhr, status, error) {
                        // Handle error
                        alert('Error fetching edit form data: ' + error);
                    }
                });
            }

            // Function to show task details
            function showDetails(task) {
                // AJAX request to fetch task details
                $.ajax({
                    type: 'POST',
                    url: 'fetchTaskDetails.php', // Replace with the actual URL of your PHP script
                    data: {
                        task_id: task.task_id
                    }, // Send task ID to fetch corresponding task details
                    success: function(response) {
                        // Display task details as alert
                        alert(response);
                    },
                    error: function(xhr, status, error) {
                        // Handle error
                        alert('Error fetching task details: ' + error);
                    }
                });
            }
        </script>
</body>

</html>