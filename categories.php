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
    $stmt = $con->prepare("DELETE FROM categories WHERE ID_category = :id");
    $stmt->execute([':id' => $id]);
    // No need to redirect here, handle this via AJAX
    exit(); // Exit to prevent further execution
}

// Fetch all categories from the database
$stmt = $con->query("SELECT ID_category, category_name FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
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
 width: 90%;
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
<?php include './pages/header.php'; ?>

<body class="bg-gray-200">

<div class="flex">
    <div class="w-4/5 p-8">
        <!-- Header -->
        <header class=" header bg-black text-white mb-8">
            <div class="container mx-auto px-6 py-3 flex justify-between items-center">
                <!-- Logo -->
                <div>
                    <h1 class="text-xl font-bold">Manage Categories</h1>
                </div>
                <div class="voltage-button flex  text-white">
                <button id="addCategoryBtn" class=" text-white px-4 py-2 ">Add Category</button>
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
        </header>

        <!-- Main Content Here -->
        <h2 class="h2 text-2xl font-bold mb-4">Category Management</h2>

        <!-- Categories Table -->
        <div class="table bg-white p-4 rounded-lg shadow-md">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b border-gray-200">ID Category</th>
                        <th class="py-2 px-4 border-b border-gray-200">Category Name</th>
                        <th class="py-2 px-4 border-b border-gray-200">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($categories)) {
                        // Output data for each row
                        foreach ($categories as $category) {
                            echo "<tr>";
                            echo "<td class='py-2 px-4 border-b border-gray-200'>" . $category["ID_category"] . "</td>";
                            echo "<td class='py-2 px-4 border-b border-gray-200'>" . $category["category_name"] . "</td>";
                            echo "<td class='py-2 px-4 border-b border-gray-200'>";
                            echo "<a href='#' class='editCategoryBtn text-blue-600 hover:text-blue-800' data-id='" . $category["ID_category"] . "' data-name='" . $category["category_name"] . "'>Edit</a> |";
                            echo "<button class='deleteCategoryBtn text-red-600 hover:text-red-800' data-id='" . $category["ID_category"] . "'>Delete</button>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' class='py-2 px-4 border-b border-gray-200 text-center'>No categories found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Add Category
    $('#addCategoryBtn').click(function() {
        // Show SweetAlert input form for adding category
        Swal.fire({
            title: 'Add Category',
            input: 'text',
            inputPlaceholder: 'Category Name',
            showCancelButton: true,
            confirmButtonText: 'Add',
            showLoaderOnConfirm: true,
            preConfirm: (categoryName) => {
                // Send AJAX request to add category
                return $.ajax({
                    url: 'add_category.php',
                    type: 'POST',
                    data: {
                        category_name: categoryName
                    },
                    dataType: 'json' // Specify JSON as the expected response type
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            // Handle success or error response
            if (result.isConfirmed) {
                if (result.value.success) {
                    // Add the newly added category to the category list
                    var newCategory = result.value.category;
                    var newRow = "<tr>" +
                        "<td class='py-2 px-4 border-b border-gray-200'>" + newCategory.ID_category + "</td>" +
                        "<td class='py-2 px-4 border-b border-gray-200'>" + newCategory.category_name + "</td>" +
                        "<td class='py-2 px-4 border-b border-gray-200'>" +
                        "<a href='#' class='editCategoryBtn text-blue-600 hover:text-blue-800' data-id='" + newCategory.ID_category + "' data-name='" + newCategory.category_name + "'>Edit</a> |" +
                        "<button class='deleteCategoryBtn text-red-600 hover:text-red-800' data-id='" + newCategory.ID_category + "'>Delete</button>" +
                        "</td>" +
                        "</tr>";

                    // Append the new row to the table body
                    $('table tbody').append(newRow);

                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Category Added Successfully!'
                    });
                } else {
                    // Show error message if needed
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: result.value.error || 'Failed to add category!'
                    });
                }
            }
        });
    });

    // Edit Category
    $(document).on('click', '.editCategoryBtn', function() {
        const categoryId = $(this).data('id');
        const categoryName = $(this).data('name');

        // Show SweetAlert input form for editing category
        Swal.fire({
            title: 'Edit Category',
            input: 'text',
            inputValue: categoryName,
            inputPlaceholder: 'Category Name',
            showCancelButton: true,
            confirmButtonText: 'Save',
            showLoaderOnConfirm: true,
            preConfirm: (newCategoryName) => {
                // Send AJAX request to edit category
                return $.ajax({
                    url: 'edit_category.php',
                    type: 'POST',
                    data: {
                        categoryID: categoryId, // Change to categoryID
                        categoryName: newCategoryName // Change to categoryName
                    },
                    dataType: 'json' // Specify JSON as the expected response type
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            // Handle success or error response
            if (result.isConfirmed) {
                if (result.value.success) {
                    // Update the category name in the UI
                    $(`.editCategoryBtn[data-id='${categoryId}']`).data('name', result.value.categoryName);
                    $(`.editCategoryBtn[data-id='${categoryId}']`).closest('tr').find('td:eq(1)').text(result.value.categoryName);
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Category Updated Successfully!'
                    });
                } else {
                    // Show error message if needed
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: result.value.error || 'Failed to edit category!'
                    });
                }
            }
        });
    });

    // Delete Category
    $(document).on('click', '.deleteCategoryBtn', function() {
        const categoryId = $(this).data('id');
        // Show confirmation dialog before deleting category
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Send AJAX request to delete category
                $.ajax({
                    url: 'categories.php?delete=' + categoryId,
                    type: 'GET',
                    success: function(response) {
                        // Reload page or update UI as needed
                        window.location.reload();
                    },
                    error: function(xhr, status, error) {
                        // Show error message if needed
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Failed to delete category!'
                        });
                    }
                });
            }
        });
    });
});
</script>

</body>
</html>
