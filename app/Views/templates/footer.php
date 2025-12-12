<!-- ✅ FOOTER -->


<!-- ✅ Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- ✅ Optional jQuery (for AJAX) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<?php if (session()->get('logged_in')): ?>
<script>
(function($) {
    const bellWrapper = $('#notificationBellWrapper');
    if (!bellWrapper.length) {
        return;
    }

    const badge      = $('#headerNotificationBadge');
    const dropdown   = $('#headerNotificationDropdown');
    const list       = $('#headerNotificationList');
    const refreshBtn = $('#refreshNotificationsBtn');

    const notificationsUrl      = '<?= base_url("notifications") ?>';
    const markAsReadBaseUrl      = '<?= base_url("notifications/mark_read") ?>';
    const markAsUnreadBaseUrl    = '<?= base_url("notifications/mark_unread") ?>';
    const csrfName               = $('meta[name="csrf-token-name"]').attr('content') || '<?= csrf_token() ?>';
    let csrfHash                 = $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>';

    function updateBadge(count, isNew = false) {
        if (count && count > 0) {
            badge.removeClass('d-none').text(count);
            if (isNew) {
                // Add pulse animation for new notifications
                badge.addClass('pulse');
                setTimeout(function() {
                    badge.removeClass('pulse');
                }, 500);
            }
        } else {
            badge.addClass('d-none').text('0');
        }
    }

    function renderNotifications(data, unreadCount = null, isNew = false) {
        if (!data || !Array.isArray(data.list) || data.list.length === 0) {
            list.html('<p class="text-center text-muted small mb-0">You are all caught up.</p>');
            updateBadge(0, false);
            return;
        }
        
        // Update badge if count is provided
        if (unreadCount !== null) {
            updateBadge(unreadCount, isNew);
        } else {
            updateBadge(data.unread || 0, false);
        }

        const items = data.list.map(function(item) {
            const createdAt = item.created_at ? `<small class="text-secondary d-block mb-1">${item.created_at}</small>` : '';
            const isUnread = String(item.is_read) === '0';
            const itemClass = isUnread ? 'notification-item unread' : 'notification-item read';
            const buttonHtml = isUnread
                ? `<button type="button" class="btn btn-sm btn-outline-primary mark-read-btn" data-id="${item.id}">
                        Mark as read
                   </button>`
                : `<button type="button" class="btn btn-sm btn-outline-secondary mark-unread-btn" data-id="${item.id}">
                        Mark as unread
                   </button>`;

            return `
                <div class="${itemClass}">
                    ${createdAt}
                    <p class="mb-2 text-dark">${item.message}</p>
                    ${buttonHtml}
                </div>
            `;
        });

        list.html(items.join(''));
        // Badge is updated in loadNotifications function
    }

    // Track previous unread count to detect new notifications
    let previousUnreadCount = 0;
    let notificationCheckInterval = null;

    function closeDropdown() {
        dropdown.removeClass('show');
    }

    bellWrapper.on('click', function(event) {
        event.stopPropagation();
        dropdown.toggleClass('show');

        if (dropdown.hasClass('show')) {
            loadNotifications(false, false);
        }
    });

    refreshBtn.on('click', function(event) {
        event.stopPropagation();
        loadNotifications(true, false);
    });

    $(document).on('click', function(event) {
        if (!$(event.target).closest('#notificationBellWrapper, #headerNotificationDropdown').length) {
            closeDropdown();
        }
    });

    $(document).on('click', '.mark-read-btn', function(event) {
        event.stopPropagation();
        const notificationId = $(this).data('id');
        if (!notificationId) {
            return;
        }

        $.post(`${markAsReadBaseUrl}/${notificationId}`, {
            [csrfName]: csrfHash
        })
        .done(function(response) {
            if (response && response.csrfToken) {
                csrfHash = response.csrfToken;
                $('meta[name="csrf-token"]').attr('content', csrfHash);
            }
            loadNotifications(false);
        });
    });

    $(document).on('click', '.mark-unread-btn', function(event) {
        event.stopPropagation();
        const notificationId = $(this).data('id');
        if (!notificationId) {
            return;
        }

        $.post(`${markAsUnreadBaseUrl}/${notificationId}`, {
            [csrfName]: csrfHash
        })
        .done(function(response) {
            if (response && response.csrfToken) {
                csrfHash = response.csrfToken;
                $('meta[name="csrf-token"]').attr('content', csrfHash);
            }
            loadNotifications(false, false);
        });
    });

    function loadNotifications(showLoader = false, checkForNew = false) {
        if (showLoader) {
            list.html('<p class="text-center text-muted small mb-0">Loading...</p>');
        }

        $.get(notificationsUrl)
            .done(function(response) {
                const currentUnreadCount = response.unread || 0;
                
                // Check if there are new notifications
                const isNewNotification = checkForNew && currentUnreadCount > previousUnreadCount;
                if (isNewNotification && previousUnreadCount >= 0) {
                    // New notification arrived!
                    const newCount = currentUnreadCount - previousUnreadCount;
                    showNewNotificationAlert(newCount);
                    animateBell();
                    updateBadge(currentUnreadCount, true);
                } else {
                    updateBadge(currentUnreadCount, false);
                }
                
                previousUnreadCount = currentUnreadCount;
                renderNotifications(response, currentUnreadCount, isNewNotification);
            })
            .fail(function() {
                if (!showLoader) {
                    // Only show error if manually refreshing
                    list.html('<p class="text-center text-danger small mb-0">Unable to load notifications.</p>');
                }
            });
    }

    function showNewNotificationAlert(count) {
        // Create a temporary alert for new notifications
        const alertHtml = `
            <div class="alert alert-info alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" 
                 style="z-index: 9999; min-width: 300px;" role="alert">
                <i class="bi bi-bell-fill me-2"></i>
                <strong>${count} new notification${count > 1 ? 's' : ''}!</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        $('body').append(alertHtml);
        
        // Auto-remove after 5 seconds
        setTimeout(function() {
            $('.alert-info').fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }

    function animateBell() {
        // Add shake animation to bell wrapper
        bellWrapper.addClass('shake');
        
        // Remove animation class after animation completes
        setTimeout(function() {
            bellWrapper.removeClass('shake');
        }, 500);
    }

    // Initial load
    loadNotifications(false, false);
    
    // Real-time updates every 10 seconds (more frequent than before)
    notificationCheckInterval = setInterval(function() {
        loadNotifications(false, true);
    }, 10000); // 10 seconds for real-time feel

    // Clean up interval when page is hidden (to save resources)
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            if (notificationCheckInterval) {
                clearInterval(notificationCheckInterval);
                notificationCheckInterval = null;
            }
        } else {
            if (!notificationCheckInterval) {
                loadNotifications(false, false);
                notificationCheckInterval = setInterval(function() {
                    loadNotifications(false, true);
                }, 10000);
            }
        }
    });
})(jQuery);
</script>
<?php endif; ?>

</body>
</html>
