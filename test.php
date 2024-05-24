<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Show Form</title>
<style>
    /* CSS for the form */
    .form-container {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .form-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
    }

    /* Close button style */
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>
</head>
<body>

<!-- Button to trigger the form -->
<button onclick="showForm('normal')">Show Form</button>

<!-- Button to show the form like an alert -->
<button onclick="showForm('alert')">Show Form Like Alert</button>

<!-- Form container -->
<div id="formContainer" class="form-container">
    <div class="form-content">
        <span class="close" onclick="closeForm()">&times;</span>
        <h2>Form Title</h2>
        <p>Form Description</p>
        <form id="myForm">
            <!-- Your form fields go here -->
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required><br><br>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br><br>
            <input type="button" value="Submit" onclick="submitForm()">
        </form>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    // Function to show the form
    function showForm(type) {
        var formContainer = $("#formContainer");
        if (type === 'alert') {
            formContainer.show();
        } else {
            formContainer.show();
            formContainer.css("background-color", "rgba(0, 0, 0, 0)");
        }
    }

    // Function to close the form
    function closeForm() {
        $("#formContainer").hide();
    }

    // Function to submit the form using Ajax
    function submitForm() {
        var formData = $("#myForm").serialize();
        $.ajax({
            type: "POST",
            url: "pdf.php", // Change this to your PHP script URL
            data: formData,
            success: function(response) {
                // Handle the response here
                alert("Form submitted successfully!");
                closeForm(); // Close the form after submission
            },
            error: function(xhr, status, error) {
                alert("An error occurred while submitting the form.");
                console.error(xhr.responseText);
            }
        });
    }
</script>

</body>
</html>
