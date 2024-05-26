<?php
// Include the database connection script
include './connexion.php';

// Check if user ID is provided
if (isset($_GET['ID_user'])) {
    $userId = $_GET['ID_user'];

    // Fetch tasks for the selected user
    $stmt = $con->prepare("SELECT * FROM tasks WHERE ID_user = :ID_user");
    $stmt->execute([':ID_user' => $userId]);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Display tasks in a card format
    if (!empty($tasks)) {
        echo "<div class='flex flex-wrap'>";
        foreach ($tasks as $task) {
            echo "<div class='bg-white shadow-md rounded p-4 m-2 w-full sm:w-1/2 lg:w-1/4'>";
            echo "<h3 class='text-lg font-bold mb-2'>" . htmlspecialchars($task['task_title']) . "</h3>";
            echo "<p class='text-gray-700 mb-2'><strong>Task ID:</strong> " . htmlspecialchars($task['task_id']) . "</p>";
            echo "<p class='text-gray-700 mb-2'><strong>Description:</strong> " . htmlspecialchars($task['task_description']) . "</p>";
            echo "<p class='text-gray-700 mb-2'><strong>Priority:</strong> " . htmlspecialchars($task['priority']) . "</p>";
            echo "<p class='text-gray-700 mb-2'><strong>Due Date:</strong> " . htmlspecialchars($task['due_date']) . "</p>";
            echo "<p class='text-gray-700 mb-2'><strong>Completed:</strong> " . ($task['is_completed'] ? 'Yes' : 'No') . "</p>";
            echo "<p class='text-gray-700 mb-2'><strong>Category ID:</strong> " . htmlspecialchars($task['ID_category']) . "</p>";
            echo "<p class='text-gray-700 mb-4'><strong>User ID:</strong> " . htmlspecialchars($task['ID_user']) . "</p>";
            echo "<div class='flex justify-between items-center'>";
            echo "<button class='editTaskBtn text-blue-600 hover:text-blue-800' data-id='" . htmlspecialchars($task["task_id"]) . "'>
                    <i class='fas fa-edit'></i>
                  </button>";
            echo "<button class='deleteTaskBtn text-red-600 hover:text-red-800' data-id='" . htmlspecialchars($task["task_id"]) . "'>
                    <i class='fas fa-trash'></i>
                  </button>";
            echo "</div>";
            echo "</div>";
        }
        echo "</div>";
    } else {
        echo "<p>No tasks found for this user.</p>";
    }
} else {
    echo "<p>No user ID provided.</p>";
}
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
<script>
$(document).ready(function() {
    // Delete Task
    $('.deleteTaskBtn').click(function() {
        const taskId = $(this).data('id');

        // Confirm deletion with SweetAlert
        Swal.fire({
            title: 'Delete Task',
            text: 'Are you sure you want to delete this task?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Send AJAX request to delete task
                $.ajax({
                    type: 'POST',
                    url: 'delete_task.php',
                    data: {
                        task_id: taskId
                    },
                    success: function(response) {
                        // Reload tasks after deletion
                        $('#showTasksBtn').click();
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong!'
                        });
                    }
                });
            }
        });
    });

    // Edit Task
    $('.editTaskBtn').click(function() {
        const taskId = $(this).data('id');

        // Redirect to edit task page
        window.location.href = 'edit_task.php?task_id=' + taskId;
    });
});
</script>
