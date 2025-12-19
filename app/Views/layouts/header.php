<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Bussin\' Foodie - Admin Dashboard' ?></title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/css/tailwind.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
    
    <style>
        /* Prevent body scroll when sidebar is open on mobile */
        body.sidebar-open {
            overflow: hidden;
        }
        
        /* Smooth transitions */
        .sidebar-transition {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .overlay-transition {
            transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Ensure full viewport height */
        html, body {
            height: 100%;
        }
        
        /* Custom scrollbar for sidebar */
        #sidebar::-webkit-scrollbar {
            width: 4px;
        }
        
        #sidebar::-webkit-scrollbar-track {
            background: #1f2937;
        }
        
        #sidebar::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 2px;
        }
        
        #sidebar::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }
        
        /* Hide scrollbar for Firefox */
        #sidebar {
            scrollbar-width: thin;
            scrollbar-color: #4b5563 #1f2937;
        }
        
        /* Better mobile handling */
        @media (max-width: 767px) {
            .mobile-full-height {
                height: 100vh;
                height: -webkit-fill-available;
            }
        }
    </style>
</head>
<body class="bg-gray-100 h-full">
    <?php if (Auth::isLoggedIn()): ?>
    <!-- Mobile sidebar overlay -->
    <div id="sidebarOverlay" 
         class="overlay-transition fixed inset-0 bg-black bg-opacity-50 z-20 hidden md:hidden"
         onclick="toggleSidebar()"></div>
    
    <!-- Mobile sidebar toggle button -->
    <button id="sidebarToggle" 
            class="md:hidden fixed top-3 left-3 z-30 p-2.5 bg-orange-500 text-white rounded-lg shadow-lg hover:bg-orange-600 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2"
            onclick="toggleSidebar()"
            aria-label="Toggle sidebar">
        <i class="fas fa-bars text-base"></i>
    </button>
    <?php endif; ?>