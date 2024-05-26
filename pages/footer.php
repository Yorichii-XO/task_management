<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Short Navbar Example</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Add any icon library like Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<style>
    body {
    margin: 0;
    font-family: Arial, sans-serif;
}

.main-header {
    /* Add styles for the main header if necessary */
}

.short-navbar {
    background-color: red;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
}

.short-navbar .logo img {
    height: 40px; /* Adjust the height as needed */
}

.short-navbar .nav-links a {
    color: white;
    text-decoration: none;
    margin: 0 15px;
}

.short-navbar .nav-links a:hover {
    text-decoration: underline;
}

.short-navbar .contact-info {
    display: flex;
    align-items: center;
}

.short-navbar .contact-info span {
    margin-right: 10px;
}

.short-navbar .contact-info i {
    margin-left: 5px;
}

</style>
<body>
    <!-- First Header -->
    <header class="main-header">
        <!-- Add content for the main header here if needed -->
    </header>
    
    <!-- Short Navbar -->
    <header class="short-navbar">
        <div class="logo">
            <img src="logo.png" alt="Logo"> <!-- Replace with your logo -->
        </div>
        <nav class="nav-links">
            <a href="#">Home</a>
            <a href="#">About Us</a>
            <a href="#">Contacts</a>
            <a href="#">Tasks</a>
        </nav>
        <div class="contact-info">
            <span>Contact: 123-456-7890</span>
            <i class="fas fa-phone"></i>
            <i class="fas fa-envelope"></i>
        </div>
    </header>
    
</body>
</html>
