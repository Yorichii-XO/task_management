<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role_type'] != 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-200">

<div class="flex">
    <!-- Sidebar -->
    <div class="bg-gray-800 text-white h-screen w-1/5">
        <div class="p-4">
            <ul class="mt-4">
                <li class="mb-2"><a href="dashboard.php" class="block px-4 py-2 hover:bg-gray-700">Dashboard</a></li>
                <li class="mb-2"><a href="../users.php" class="block px-4 py-2 hover:bg-gray-700">Users</a></li>
                <li class="mb-2"><a href="../tasks.php" class="block px-4 py-2 hover:bg-gray-700">Tasks</a></li>
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
                    <h1 class="text-xl font-bold">Admin Dashboard</h1>
                </div>
                <!-- Search bar -->
                <div class="flex items-center">
                    <input type="text" class="px-4 py-2 rounded-lg border-gray-300 focus:outline-none focus:border-blue-400" placeholder="Search...">
                    <button class="text-gray-500 ml-2 focus:outline-none">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <!-- Icons -->
                <div class="flex items-center">
                    <button class="text-gray-500 focus:outline-none">
                        <i class="fas fa-bell"></i>
                    </button>
                    <button class="text-gray-500 ml-6 focus:outline-none">
                        <i class="fas fa-cog"></i>
                    </button>
                </div>
            </div>
        </header>

        <!-- Welcome message -->
        <h1 class="text-3xl font-bold mb-8">Welcome to the Admin Dashboard, <?php echo $_SESSION['username']; ?></h1>

        <!-- Users Section -->
        <section class="mb-8">
            <h2 class="text-2xl font-bold mb-4">Manage Users</h2>
            <!-- Add buttons for CRUD operations on users -->
            <div class="flex space-x-4">
                <a href="add_user.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Add User</a>
                <a href="edit_user.php" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Edit User</a>
                <a href="delete_user.php" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Delete User</a>
            </div>
        </section>

        <!-- Tasks Section -->
        <section>
            <h2 class="text-2xl font-bold mb-4">Manage Tasks</h2>
            <!-- Add buttons for CRUD operations on tasks -->
            <div class="flex space-x-4">
                <a href="all_tasks.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Show All Tasks</a>
            </div>
        </section>
    </div>
</div>

</body>
</html>
