<?php require_once __DIR__ . '/config/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Story | JUTO</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600;700&family=Nunito+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { serif: ['"Cormorant Garamond"', 'serif'], sans: ['"Nunito Sans"', 'sans-serif'] } } } }
    </script>
</head>
<body class="bg-[#fefdfc] flex flex-col min-h-screen">
    <?php include 'includes/header.php'; ?>
    <main class="flex-grow pt-32 pb-32">
        <div class="max-w-[800px] mx-auto px-6 text-center">
            <h1 class="font-serif text-[40px] text-[#4d3c31] uppercase tracking-widest mb-8">Our Story</h1>
            <div class="w-12 h-[2px] bg-[#d4af37] mx-auto mb-10"></div>
            <p class="font-sans text-[#71685f] text-[17px] leading-loose">Discover the heritage and passion behind JUTO. From our humble beginnings to becoming a global symbol of refined craftsmanship.</p>
            <p class="font-sans text-[#71685f] text-[15px] leading-loose mt-8">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
        </div>
    </main>
    <?php include 'includes/footer.php'; ?>
</body>
</html>