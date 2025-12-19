/**
 * UI interactions and micro-animations for Bussin' Foodie Dashboard
 */

class UIHelper {
    constructor() {
        this.init();
    }
    
    init() {
        // Initialize sidebar
        this.initSidebar();
        
        // Initialize tooltips
        this.initTooltips();
        
        // Initialize status update buttons
        this.initStatusButtons();
        
        // Initialize confirmation dialogs for delete actions
        this.initDeleteConfirmations();
        
        // Add hover effects to table rows
        this.initTableHover();
        
        // Initialize copy to clipboard buttons
        this.initClipboard();
        
        // Handle window resize
        this.handleResize();
    }
    
    initSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('sidebarToggle');
        
        if (!sidebar || !toggleBtn) return;
        
        // Ensure sidebar is closed on mobile on page load
        if (window.innerWidth < 768) {
            sidebar.classList.add('-translate-x-full');
            if (overlay) {
                overlay.classList.add('hidden');
                overlay.classList.remove('opacity-100');
            }
            document.body.classList.remove('sidebar-open');
            
            // Set hamburger icon
            const icon = toggleBtn.querySelector('i');
            if (icon) {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        } else {
            // Ensure sidebar is open on desktop
            sidebar.classList.remove('-translate-x-full');
            if (overlay) {
                overlay.classList.add('hidden');
            }
        }
        
        // Auto-close sidebar on mobile when clicking outside
        document.addEventListener('click', (e) => {
            if (window.innerWidth < 768 && 
                sidebar && 
                !sidebar.classList.contains('-translate-x-full') &&
                !sidebar.contains(e.target) &&
                (!toggleBtn || !toggleBtn.contains(e.target)) &&
                overlay && e.target === overlay) {
                this.toggleSidebar();
            }
        });
        
        // Close sidebar with ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && sidebar && !sidebar.classList.contains('-translate-x-full')) {
                this.toggleSidebar();
            }
        });
    }
    
    toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('sidebarToggle');
        const body = document.body;
        
        if (!sidebar || !overlay) return;
        
        if (sidebar.classList.contains('-translate-x-full')) {
            // Open sidebar
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            setTimeout(() => {
                overlay.classList.add('opacity-100');
            }, 10);
            body.classList.add('sidebar-open');
            
            // Update toggle button icon
            if (toggleBtn) {
                const icon = toggleBtn.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                }
            }
        } else {
            // Close sidebar
            sidebar.classList.add('-translate-x-full');
            overlay.classList.remove('opacity-100');
            setTimeout(() => {
                overlay.classList.add('hidden');
            }, 300);
            body.classList.remove('sidebar-open');
            
            // Update toggle button icon
            if (toggleBtn) {
                const icon = toggleBtn.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
        }
    }
    
    initTooltips() {
        // Create tooltip container
        const tooltip = document.createElement('div');
        tooltip.id = 'custom-tooltip';
        tooltip.className = 'fixed z-50 px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 pointer-events-none transition-all duration-200';
        document.body.appendChild(tooltip);
        
        // Add event listeners to elements with data-tooltip attribute
        document.querySelectorAll('[data-tooltip]').forEach(el => {
            el.addEventListener('mouseenter', (e) => {
                const rect = el.getBoundingClientRect();
                tooltip.textContent = el.getAttribute('data-tooltip');
                
                // Position tooltip
                const top = rect.top - tooltip.offsetHeight - 10;
                const left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2);
                
                tooltip.style.left = Math.max(10, Math.min(left, window.innerWidth - tooltip.offsetWidth - 10)) + 'px';
                tooltip.style.top = Math.max(10, top) + 'px';
                tooltip.style.opacity = '1';
            });
            
            el.addEventListener('mouseleave', () => {
                tooltip.style.opacity = '0';
            });
        });
    }
    
    initStatusButtons() {
        const statusButtons = document.querySelectorAll('.status-update');
        statusButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                
                const orderId = button.getAttribute('data-order-id');
                const currentStatus = button.getAttribute('data-current-status');
                const newStatus = button.getAttribute('data-new-status');
                
                if (typeof Swal === 'undefined') {
                    if (confirm(`Change status from ${currentStatus} to ${newStatus}?`)) {
                        // Submit the form
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '/orders/update-status';
                        
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = 'csrf_token';
                        csrfInput.value = document.querySelector('[name="csrf_token"]')?.value || '';
                        
                        const orderIdInput = document.createElement('input');
                        orderIdInput.type = 'hidden';
                        orderIdInput.name = 'order_id';
                        orderIdInput.value = orderId;
                        
                        const statusInput = document.createElement('input');
                        statusInput.type = 'hidden';
                        statusInput.name = 'status';
                        statusInput.value = newStatus;
                        
                        form.appendChild(csrfInput);
                        form.appendChild(orderIdInput);
                        form.appendChild(statusInput);
                        document.body.appendChild(form);
                        form.submit();
                    }
                } else {
                    Swal.fire({
                        title: 'Update Order Status?',
                        text: `Change status from ${currentStatus} to ${newStatus}?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, update it!',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Submit the form
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '/orders/update-status';
                            
                            const csrfInput = document.createElement('input');
                            csrfInput.type = 'hidden';
                            csrfInput.name = 'csrf_token';
                            csrfInput.value = document.querySelector('[name="csrf_token"]')?.value || '';
                            
                            const orderIdInput = document.createElement('input');
                            orderIdInput.type = 'hidden';
                            orderIdInput.name = 'order_id';
                            orderIdInput.value = orderId;
                            
                            const statusInput = document.createElement('input');
                            statusInput.type = 'hidden';
                            statusInput.name = 'status';
                            statusInput.value = newStatus;
                            
                            form.appendChild(csrfInput);
                            form.appendChild(orderIdInput);
                            form.appendChild(statusInput);
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                }
            });
        });
    }
    
    initDeleteConfirmations() {
        const deleteButtons = document.querySelectorAll('.delete-confirm');
        deleteButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                
                const itemName = button.getAttribute('data-item-name') || 'this item';
                const itemType = button.getAttribute('data-item-type') || 'item';
                
                if (typeof Swal === 'undefined') {
                    if (confirm(`Are you sure you want to delete ${itemName}? This action cannot be undone.`)) {
                        // Submit the form
                        const form = button.closest('form');
                        if (form) {
                            form.submit();
                        } else if (button.href) {
                            // If button is a link, navigate to it
                            window.location.href = button.href;
                        }
                    }
                } else {
                    Swal.fire({
                        title: `Delete ${itemType}?`,
                        text: `Are you sure you want to delete ${itemName}? This action cannot be undone.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Submit the form
                            const form = button.closest('form');
                            if (form) {
                                form.submit();
                            } else if (button.href) {
                                // If button is a link, navigate to it
                                window.location.href = button.href;
                            }
                        }
                    });
                }
            });
        });
    }
    
    initTableHover() {
        const tableRows = document.querySelectorAll('tbody tr');
        tableRows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.classList.add('bg-gray-50', 'transition-colors', 'duration-150');
            });
            
            row.addEventListener('mouseleave', function() {
                this.classList.remove('bg-gray-50');
            });
        });
    }
    
    initClipboard() {
        const copyButtons = document.querySelectorAll('.copy-to-clipboard');
        copyButtons.forEach(button => {
            button.addEventListener('click', function() {
                const textToCopy = this.getAttribute('data-clipboard-text');
                
                navigator.clipboard.writeText(textToCopy).then(() => {
                    // Show success feedback
                    const originalHTML = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-check mr-1"></i> Copied!';
                    this.classList.add('bg-green-500', 'text-white');
                    
                    setTimeout(() => {
                        this.innerHTML = originalHTML;
                        this.classList.remove('bg-green-500', 'text-white');
                    }, 2000);
                }).catch(err => {
                    console.error('Failed to copy: ', err);
                });
            });
        });
    }
    
    handleResize() {
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                const toggleBtn = document.getElementById('sidebarToggle');
                
                if (window.innerWidth >= 768) {
                    // Desktop: ensure sidebar is open
                    if (sidebar) {
                        sidebar.classList.remove('-translate-x-full');
                    }
                    if (overlay) {
                        overlay.classList.add('hidden');
                        overlay.classList.remove('opacity-100');
                    }
                    document.body.classList.remove('sidebar-open');
                    
                    // Update toggle button icon
                    if (toggleBtn) {
                        const icon = toggleBtn.querySelector('i');
                        if (icon) {
                            icon.classList.remove('fa-times');
                            icon.classList.add('fa-bars');
                        }
                    }
                } else {
                    // Mobile: ensure sidebar is closed
                    if (sidebar && !sidebar.classList.contains('-translate-x-full')) {
                        sidebar.classList.add('-translate-x-full');
                    }
                    if (overlay) {
                        overlay.classList.add('hidden');
                        overlay.classList.remove('opacity-100');
                    }
                    document.body.classList.remove('sidebar-open');
                }
            }, 250);
        });
    }
}

// Initialize UI when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    const ui = new UIHelper();
    
    // Make toggleSidebar function globally available
    window.toggleSidebar = function() {
        ui.toggleSidebar();
    };
});