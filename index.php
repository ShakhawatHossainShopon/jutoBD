<?php
include "config/config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juto Leather Goods</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=Nunito+Sans:ital,opsz,wght@0,6..12,300;0,6..12,400;0,6..12,600;0,6..12,700;0,6..12,800;1,6..12,400&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'juto-brown': '#473121',
                        'juto-gray': '#6d655f',
                        'juto-dark': '#2c2521',
                    },
                    fontFamily: {
                        sans: ['"Nunito Sans"', 'sans-serif'],
                        serif: ['"Cormorant Garamond"', 'serif'],
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer base {
            html {
                scroll-behavior: smooth;
            }
            /* Premium Custom Scrollbar */
            ::-webkit-scrollbar {
                width: 8px;
            }
            ::-webkit-scrollbar-track {
                background: #f4f2eb; 
            }
            ::-webkit-scrollbar-thumb {
                background: #a3998f; 
                border-radius: 4px;
            }
            ::-webkit-scrollbar-thumb:hover {
                background: #473121; 
            }
            /* Branded Text Selection */
            ::selection {
                background-color: #473121;
                color: #ffffff;
            }
            h1, h2, h3, h4, h5, h6 {
                @apply font-serif;
            }
        }
        
        /* 3D Preloader */
        #page-loader {
            position: fixed;
            inset: 0;
            z-index: 99999;
            background-color: #f4f2eb;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: opacity 0.6s ease-out, visibility 0.6s ease-out;
        }
        .loader-text {
            font-size: 3.5rem;
            color: #2c313a;
            letter-spacing: 0.15em;
            animation: float-pulse 1.5s infinite ease-in-out alternate;
            transform-style: preserve-3d;
        }
        
        @keyframes float-pulse {
            0% { transform: scale(0.95) translateY(0px); text-shadow: 0 5px 15px rgba(0,0,0,0.05); }
            100% { transform: scale(1.05) translateY(-10px); text-shadow: 0 25px 30px rgba(0,0,0,0.15); }
        }
    </style>
</head>
<body class="bg-white text-slate-800 font-sans antialiased flex flex-col min-h-screen overflow-x-hidden">

    <!-- Preloader -->
    <div id="page-loader">
        <div class="loader-text font-serif font-bold">JUTO</div>
    </div>

    <!-- Include Navbar -->
    <?php include 'includes/header.php'; ?>

    <!-- Main Content Area -->
    <main class="flex-grow">
        <?php include 'includes/sections/hero.php'; ?>
        <?php include 'includes/sections/new_arrivals.php'; ?>
        <?php include 'includes/sections/brand_story.php'; ?>
        <?php include 'includes/sections/categories.php'; ?>
        <?php include 'includes/sections/why_juto.php'; ?>
        <?php include 'includes/sections/marquee.php'; ?>
        <?php include 'includes/sections/cta_banner.php'; ?>
    </main>

    <!-- Include Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- UX & Animation Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // 1. Remove Preloader Smoothly
        window.addEventListener('load', function() {
            const loader = document.getElementById('page-loader');
            loader.style.opacity = '0';
            setTimeout(() => {
                loader.style.visibility = 'hidden';
            }, 600); // Wait for transition to finish
        });

        // 2. Initialize AOS (Animate On Scroll)
        // This allows us to easily add data-aos="fade-up" to future sections
        AOS.init({
            duration: 900,
            easing: 'ease-out-cubic',
            once: true,
            offset: 80,
            delay: 50
        });
    </script>
</body>
</html>
