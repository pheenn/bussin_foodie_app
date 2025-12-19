    <?php if (Auth::isLoggedIn()): ?>
        </main> <!-- Close mainContent -->
    </div> <!-- Close main content area -->
</div> <!-- Close main container from sidebar -->
    <?php endif; ?>
    
    <!-- Include custom JS -->
    <script src="/js/sweetalert-init.js"></script>
    <script src="/js/validation.js"></script>
    <script src="/js/ui.js"></script>
    
    <script>
    // Sidebar toggle functionality
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const body = document.body;
        
        if (sidebar && overlay) {
            // Toggle sidebar
            if (sidebar.classList.contains('-translate-x-full')) {
                // Open sidebar
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                setTimeout(() => {
                    overlay.classList.add('opacity-100');
                }, 10);
                body.classList.add('sidebar-open');
            } else {
                // Close sidebar
                sidebar.classList.add('-translate-x-full');
                overlay.classList.remove('opacity-100');
                setTimeout(() => {
                    overlay.classList.add('hidden');
                }, 300);
                body.classList.remove('sidebar-open');
            }
            
            // Update toggle button icon
            const toggleBtn = document.getElementById('sidebarToggle');
            if (toggleBtn) {
                const icon = toggleBtn.querySelector('i');
                if (sidebar.classList.contains('-translate-x-full')) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                } else {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                }
            }
        }
    }
    
    // Initialize sidebar on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Close sidebar when clicking on a link (mobile only)
        const sidebarLinks = document.querySelectorAll('#sidebar a');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 768) {
                    toggleSidebar();
                }
            });
        });
        
        // Close sidebar when pressing ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const sidebar = document.getElementById('sidebar');
                if (sidebar && !sidebar.classList.contains('-translate-x-full')) {
                    toggleSidebar();
                }
            }
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('sidebarToggle');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (window.innerWidth < 768 && 
                sidebar && 
                !sidebar.classList.contains('-translate-x-full') &&
                !sidebar.contains(e.target) &&
                (!toggleBtn || !toggleBtn.contains(e.target)) &&
                e.target === overlay) {
                toggleSidebar();
            }
        });
        
        // Handle window resize
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            // Auto-close sidebar when switching to mobile
            if (window.innerWidth < 768) {
                if (sidebar && !sidebar.classList.contains('-translate-x-full')) {
                    sidebar.classList.add('-translate-x-full');
                }
                if (overlay) {
                    overlay.classList.add('hidden');
                    overlay.classList.remove('opacity-100');
                }
                document.body.classList.remove('sidebar-open');
            }
        });
        
        // Flash message handling
        <?php if (Flash::has('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '<?= addslashes(Flash::get('success')) ?>',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end',
                showClass: {
                    popup: 'animate__animated animate__fadeInRight'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutRight'
                }
            });
        <?php elseif (Flash::has('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '<?= addslashes(Flash::get('error')) ?>',
                timer: 4000,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                showClass: {
                    popup: 'animate__animated animate__fadeInRight'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutRight'
                }
            });
        <?php elseif (Flash::has('warning')): ?>
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: '<?= addslashes(Flash::get('warning')) ?>',
                timer: 3000,
                toast: true,
                position: 'top-end',
                showConfirmButton: false
            });
        <?php elseif (Flash::has('info')): ?>
            Swal.fire({
                icon: 'info',
                title: 'Information',
                text: '<?= addslashes(Flash::get('info')) ?>',
                timer: 3000,
                toast: true,
                position: 'top-end',
                showConfirmButton: false
            });
        <?php endif; ?>
    });
    </script>
</body>
</html>