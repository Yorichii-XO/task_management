<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role_type'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Include the database connection script
include './connexion.php';

// Handle delete operation
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $con->prepare("DELETE FROM users WHERE ID_user = :id");
    $stmt->execute([':id' => $id]);
    header("Location: users.php");
    exit();
}

// Fetch all users from the database
// Fetch all users from the database along with their roles
$stmt = $con->query("SELECT u.ID_user, u.username, u.email, u.password, r.role_type
                     FROM users u
                     JOIN roles r ON u.ID_role = r.ID_role");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch roles for dropdown
$stmt = $con->query("SELECT ID_role, role_type FROM roles");
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    <h1 class="text-xl font-bold">Manage Users</h1>
                </div>
                <!-- Add User Button -->
                <button id="addUserBtn" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Add User</button>
            </div>
        </header>

        <!-- Main Content Here -->
        <h2 class="text-2xl font-bold mb-4">User Management</h2>

        <!-- Users Table -->
        <div class="bg-white p-4 rounded-lg shadow-md">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b border-gray-200">ID User</th>
                        <th class="py-2 px-4 border-b border-gray-200">Username</th>
                        <th class="py-2 px-4 border-b border-gray-200">Email</th>
                        <th class="py-2 px-4 border-b border-gray-200">Password</th>
                        <th class="py-2 px-4 border-b border-gray-200">Role</th>
                        <th class="py-2 px-4 border-b border-gray-200">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($users)) {
                        // Output data for each row
                        foreach ($users as $user) {
                            echo "<tr>";
                            echo "<td class='py-2 px-4 border-b border-gray-200'>" . $user["ID_user"] . "</td>";
                            echo "<td class='py-2 px-4 border-b border-gray-200'>" . $user["username"] . "</td>";
                            echo "<td class='py-2 px-4 border-b border-gray-200'>" . $user["email"] . "</td>";
                            echo "<td class='py-2 px-4 border-b border-gray-200'>" . $user["password"] . "</td>";
                            echo "<td class='py-2 px-4 border-b border-gray-200'>" . $user["role_type"] . "</td>";
                            echo "<td class='py-2 px-4 border-b border-gray-200'>
                                    <button class='editUserBtn text-blue-600 hover:text-blue-800' data-id='" . $user["ID_user"] . "'>Edit</button> |
                                    <a href='users.php?delete=" . $user["ID_user"] . "' class='text-red-600 hover:text-red-800' onclick='return confirm(\"Are you sure you want to delete this user?\");'>Delete</a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='py-2 px-4 border-b border-gray-200 text-center'>No users found</td></tr>";
                    }
                    ?>
                </tbody>

            </table>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    // Add User
    $('#addUserBtn').click(function() {
        Swal.fire({
            title: 'Add User',
            html: `<input type="text" id="username" class="swal2-input" placeholder="Username">
                   <input type="email" id="email" class="swal2-input" placeholder="Email">
                   <input type="password" id="password" class="swal2-input" placeholder="Password">
                   <select id="role" class="swal2-select">
                       <?php
                       foreach ($roles as $role) {
                           echo "<option value='" . $role['ID_role'] . "'>" . $role['role_type'] . "</option>";
                       }
                       ?>
                   </select>`,
            confirmButtonText: 'Add',
            showCancelButton: true,
            preConfirm: () => {
                const username = Swal.getPopup().querySelector('#username').value;
                const email = Swal.getPopup().querySelector('#email').value;
                const password = Swal.getPopup().querySelector('#password').value;
                const role = Swal.getPopup().querySelector('#role').value;

                // Send data to server to add user
                $.ajax({
                    type: 'POST',
                    url: 'add_user.php',
                    data: {
                        username: username,
                        email: email,
                        password: password,
                        role: role
                    },
                    success: function(response) {
                        // Reload page after adding user
                        window.location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                        )
                    }
                });
            }
        });
    });

    // Edit User
    $('.editUserBtn').click(function() {
        const userId = $(this).data('id');

        // Fetch user data for editing
        $.ajax({
            type: 'POST',
            url: 'fetch_user.php',
            data: {
                id: userId
            },
            success: function(response) {
                // Display user data for editing
                Swal.fire({
                    title: 'Edit User',
                    html: response,
                    confirmButtonText: 'Save',
                    showCancelButton: true,
                    preConfirm: () => {
                        const username = $('#username').val();
                        const email = $('#email').val();
                        const password = $('#password').val();
                        const role = $('#role').val();

                        // Send data to server to update user
                        $.ajax({
                            type: 'POST',
                            url: 'edit_user.php',
                            data: {
                                ID_user: userId,
                                username: username,
                                email: email,
                                password: password,
                                ID_role: role
                            },
                            success: function(response) {
                                // Reload page after updating user
                                window.location.reload();
                            },
                            error: function(xhr, status, error) {
                                console.error(xhr.responseText);
                                Swal.showValidationMessage(
                                    `Request failed: ${error}`
                                )
                            }
                        });
                    }
                });
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
    });
});
</script>

</body>
</html>
