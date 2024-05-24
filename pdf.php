<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Here you can perform any additional validation or processing of the form data

    // Example response
    $response = array(
        'status' => 'success',
        'message' => 'Form submitted successfully!',
        'redirect' => 'test.php' // Redirect URL
    );

    // Send JSON response back to the client
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
} else {
    // If the form is not submitted via POST method, return an error response
    $response = array(
        'status' => 'error',
        'message' => 'Form submission method not allowed!'
    );

    // Send JSON response back to the client
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
