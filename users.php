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
<style>
    .header{
        margin-top: 60px;
        margin-left: 140px;
    }
    .table{
        margin-left: 140px;
 width: 88%;
    }
    .h2{
        margin-left: 140px;
 
    }
    @keyframes animateBackground {
        0% {
            background-position: 0 0;
        }

        100% {
            background-position: 100% 0;
        }
    }

    .text-background {
        font-size: 2rem;
        font-weight: bold;
        color: white;
        background: url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTaftm90ViH2M7S9_0D5_KpXy3MAz5FNQsBoGr4318yYw&s') no-repeat;
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        animation: animateBackground 10s infinite linear;
    }

    .voltage-button {
        position: relative;
       
    }

    .voltage-button button {
       
        color: white;
        padding: 1rem 3rem ;
        height: 55px;
        border-radius: 4rem;
        border: 5px solid rgb(131, 8, 8);
        font-size: 1.2rem;
        line-height: 1em;
        letter-spacing: 0.075em;
        transition: background 0.3s;
        font-size: 17px;
        font-weight: bold;
    }

    .voltage-button button:hover {
        cursor: pointer;
        background: white;
        color: black;
        font-size: 17px;
        font-weight: bold;
    }

    .voltage-button button:hover+svg,
    .voltage-button button:hover+svg+.dots {
        opacity: 1;
    }

    .voltage-button svg {
        display: block;
        position: absolute;
        top: -0.75em;
        left: -0.25em;
        width: calc(100% + 0.5em);
        height: calc(100% + 1.5em);
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.4s;
        transition-delay: 0.1s;
    }

    .voltage-button svg path {
        stroke-dasharray: 100;
        filter: url("#glow");
    }

    .voltage-button svg path.line-1 {
        stroke: white;
        stroke-dashoffset: 0;
        animation: spark-1 3s linear infinite;
    }

    .voltage-button svg path.line-2 {
        stroke: red;
        stroke-dashoffset: 500;
        animation: spark-2 3s linear infinite;
    }

    .voltage-button .dots {
        opacity: 0;
        transition: opacity 0.3s;
        transition-delay: 0.4s;
    }

    .voltage-button .dots .dot {
        width: 1rem;
        height: 1rem;
        background: white;
        border-radius: 100%;
        position: absolute;
        opacity: 0;
    }

    .voltage-button .dots .dot-1 {
        top: 0;
        left: 20%;
        animation: fly-up 3s linear infinite;
    }

    .voltage-button .dots .dot-2 {
        top: 0;
        left: 55%;
        animation: fly-up 3s linear infinite;
        animation-delay: 0.5s;
    }

    .voltage-button .dots .dot-3 {
        top: 0;
        left: 80%;
        animation: fly-up 3s linear infinite;
        animation-delay: 1s;
    }

    .voltage-button .dots .dot-4 {
        bottom: 0;
        left: 30%;
        animation: fly-down 3s linear infinite;
        animation-delay: 2.5s;
    }

    .voltage-button .dots .dot-5 {
        bottom: 0;
        left: 65%;
        animation: fly-down 3s linear infinite;
        animation-delay: 1.5s;
    }

    @keyframes spark-1 {
        to {
            stroke-dashoffset: -1000;
        }
    }

    @keyframes spark-2 {
        to {
            stroke-dashoffset: -500;
        }
    }

    @keyframes fly-up {
        0% {
            opacity: 0;
            transform: translateY(0) scale(0.2);
        }

        5% {
            opacity: 1;
            transform: translateY(-1.5rem) scale(0.4);
        }

        10%,
        100% {
            opacity: 0;
            transform: translateY(-3rem) scale(0.2);
        }
    }

    @keyframes fly-down {
        0% {
            opacity: 0;
            transform: translateY(0) scale(0.2);
        }

        5% {
            opacity: 1;
            transform: translateY(1.5rem) scale(0.4);
        }

        10%,
        100% {
            opacity: 0;
            transform: translateY(3rem) scale(0.2);
        }
    }
</style>
<body class="">
<?php include './pages/header.php'; ?>

<div class="flex">
    
    <!-- Content -->
    <div class="w-4/5 p-8">
        <!-- Header -->
        <header class="header bg-black text-white mb-8">
            <div class="container mx-auto px-6 py-3 flex justify-between items-center">
                <!-- Logo -->
                <div>
                    <h1 class="text-xl font-bold">Manage Users</h1>
                </div>
                <div class="voltage-button flex  text-white">
                <button id="addUserBtn" class=" text-white px-4 py-2 ">Add User</button>
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
            </div>
        </header>

        <!-- Main Content Here -->
        <h2 class="h2 text-2xl font-bold mb-4">User Management</h2>

        <!-- Users Table -->
        <div class="table bg-white p-4 rounded-lg shadow-md">
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
                url: '/users/add_user.php',
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
        url: '/users/fetch_user.php',
        data: {
            id: userId,
            roles: <?php echo json_encode($roles); ?> // Pass roles variable
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
                        url: '/users/edit_user.php',
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