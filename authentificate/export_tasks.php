<?php
// export_tasks.php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['export_json'])) {
        // Sample tasks data
        $tasks = [
            ["id" => 1, "title" => "Task 1", "description" => "Description of Task 1", "deadline" => "2024-05-30"],
            ["id" => 2, "title" => "Task 2", "description" => "Description of Task 2", "deadline" => "2024-06-01"],
            // Add more tasks as needed
        ];

        // Encode tasks data to JSON
        $json_tasks = json_encode($tasks);

        // Set headers to force download
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="tasks.json"');

        // Output JSON data
        echo $json_tasks;
        exit();
    }
}
?>
