<?php
// Include the database connection script
include './connexion.php';

// Check if category ID and name are provided
if (isset($_POST['categoryID'], $_POST['categoryName'])) {
    $categoryID = $_POST['categoryID'];
    $categoryName = $_POST['categoryName'];

    // Update the category name in the database
    try {
        $stmt = $con->prepare("UPDATE categories SET category_name = :categoryName WHERE ID_category = :categoryID");
        $result = $stmt->execute([':categoryName' => $categoryName, ':categoryID' => $categoryID]);

        // Check if the update was successful
        if ($result) {
            // Return success response
            echo json_encode(['success' => true, 'categoryName' => $categoryName]); // Return the updated category name
        } else {
            // Return error response
            echo json_encode(['success' => false, 'error' => 'Failed to update category']);
        }
    } catch (PDOException $e) {
        // Handle database error
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    // Return error response if category ID or name is not provided
    echo json_encode(['success' => false, 'error' => 'Category ID or name not provided']);
}
?>
