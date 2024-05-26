<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Slider</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <style>
        .swiper-container {
            width: 100%;
            height: 100vh;
        }
        .swiper-slide {
            position: relative;
        }
        .swiper-slide img {
            width: 100%;
            height: 80%;
            object-fit: cover;
        }
        .text-center {
            position: absolute;
            top: 44%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            text-align: center;
        }
        .animate-text {
            display: inline-block;
            opacity: 0;
            animation: fadeInOut 9s infinite;
        }
        @keyframes fadeInOut {
            0%, 100% { opacity: 0; }
            33%, 66% { opacity: 1; }
        }
        .animate-text:nth-child(1) {
            animation-delay: 0s;
        }
        .animate-text:nth-child(2) {
            animation-delay: 3s;
        }
        .animate-text:nth-child(3) {
            animation-delay: 6s;
        }
        .btn {
            background-color: black;
            color: white;
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: red;
        }
        
    </style>
</head>
<body class="bg-gray-900">
    <div class="swiper-container">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
                <img src="https://images.pexels.com/photos/317356/pexels-photo-317356.jpeg" alt="image2">
                <div class="text-center">
                    <h1 class="text-4xl font-bold mb-4 text-black">Task Management</h1>
                    <p class="text-lg mb-4 font-bold text-black">Effortlessly manage your tasks and boost productivity.</p>
                    <div>
                        <span class="animate-text text-2xl text-black">To Do</span>
                        <span class="animate-text text-2xl text-black">Doing</span>
                        <span class="animate-text text-2xl text-black">Done</span>
                    </div>
                    <div class="mt-8">
                        <a href="login.html" class="btn">Get Started</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Add Pagination -->
        <div class="swiper-pagination"></div>
        <!-- Add Navigation -->
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>

    <!-- Task Management Section -->
    <section class="bg-white text-black py-16">
        <div class="container mx-auto flex flex-col md:flex-row items-center">
            <div class="md:w-1/2">
                <h2 class="text-4xl font-bold mb-4">Effective Task Management</h2>
                <p class="text-lg mb-4">Manage your tasks efficiently and effectively with our comprehensive task management system. Ensure that your team is always on the same page and that deadlines are met with ease.</p>
                <p class="text-lg">Stay organized and prioritize your tasks with our user-friendly interface. Track progress, set deadlines, and achieve your goals effortlessly.</p>
            </div>
            <div class="md:w-1/2 mt-8 md:mt-0 md:ml-8">
                <img src="https://us.123rf.com/450wm/mike107/mike1071702/mike107170200227/72462036-carnet-de-notes-avec-tools-et-notes-sur-la-gestion-de-projet.jpg?ver=6" alt="Task Management Image" class="w-full h-auto object-cover rounded-lg shadow-lg">
            </div>
        </div>
    </section>
    <h2 class="text-2xl font-bold mb-4">Category Management</h2>

    <section class="bg-white text-black py-16">
        <div class="container mx-auto flex flex-col md:flex-row items-center">
            <div class="md:w-1/2">
                <h2 class="text-4xl font-bold mb-4">Effective Task Management</h2>
                <p class="text-lg mb-4">Manage your tasks efficiently and effectively with our comprehensive task management system. Ensure that your team is always on the same page and that deadlines are met with ease.</p>
                <p class="text-lg">Stay organized and prioritize your tasks with our user-friendly interface. Track progress, set deadlines, and achieve your goals effortlessly.</p>
            </div>
            <div class="md:w-1/2 mt-8 md:mt-0 md:ml-8">
                <img src="https://media.istockphoto.com/id/1485274203/photo/auditing-and-evaluating-the-quality-and-efficiency-of-personnel-checklist-with-checkmarks-and.webp?b=1&s=170667a&w=0&k=20&c=EWGYZPt92ECjZ6UmuothPa_lRE19Iw9VCpnXUtPaRis=" alt="Task Management Image" class="w-full h-auto object-cover rounded-lg shadow-lg">
            </div>
        </div>
    </section>
    <!-- Centered Text Section -->
   

    <script>
        const swiper = new Swiper('.swiper-container', {
            loop: true,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
        });
    </script>
</body>
</html>
