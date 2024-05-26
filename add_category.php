<?php
// Include the database connection script
include './connexion.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the category name is provided in the POST data
    if (isset($_POST['category_name'])) {
        $categoryName = $_POST['category_name'];
        
        // Insert the new category into the database
        try {
            $stmt = $con->prepare("INSERT INTO categories (category_name) VALUES (:categoryName)");
            $result = $stmt->execute([':categoryName' => $categoryName]);

            // Check if the insertion was successful
            if ($result) {
                // Get the ID of the newly inserted category
                $newCategoryId = $con->lastInsertId();

                // Fetch the details of the newly added category
                $stmt = $con->prepare("SELECT ID_category, category_name FROM categories WHERE ID_category = :categoryId");
                $stmt->execute([':categoryId' => $newCategoryId]);
                $newCategory = $stmt->fetch(PDO::FETCH_ASSOC);

                // Return the newly added category as JSON response
                echo json_encode(['success' => true, 'category' => $newCategory]);
            } else {
                // Return error response
                echo json_encode(['success' => false, 'error' => 'Failed to add category to database']);
            }
        } catch (PDOException $e) {
            // Handle database error
            echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        // Return error response if category name is not provided
        echo json_encode(['success' => false, 'error' => 'Category name not provided']);
    }
} else {
    // Return error response if request method is not POST
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>
