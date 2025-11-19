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

    const notificationsUrl   = '<?= base_url("notifications") ?>';
    const markAsReadBaseUrl  = '<?= base_url("notifications/mark_read") ?>';
    const csrfName           = $('meta[name="csrf-token-name"]').attr('content') || '<?= csrf_token() ?>';
    let csrfHash             = $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>';

    function updateBadge(count) {
        if (count && count > 0) {
            badge.removeClass('d-none').text(count);
        } else {
            badge.addClass('d-none').text('0');
        }
    }

    function renderNotifications(data) {
        if (!data || !Array.isArray(data.list) || data.list.length === 0) {
            list.html('<p class="text-center text-muted small mb-0">You are all caught up.</p>');
            updateBadge(0);
            return;
        }

        const items = data.list.map(function(item) {
            const createdAt = item.created_at ? `<small class="text-secondary d-block mb-1">${item.created_at}</small>` : '';
            return `
                <div class="notification-item">
                    ${createdAt}
                    <p class="mb-2 text-dark">${item.message}</p>
                    <button type="button" class="btn btn-sm btn-outline-secondary mark-read-btn" data-id="${item.id}">
                        Mark as read
                    </button>
                </div>
            `;
        });

        list.html(items.join(''));
        updateBadge(data.unread || 0);
    }

    function loadNotifications(showLoader = false) {
        if (showLoader) {
            list.html('<p class="text-center text-muted small mb-0">Loading...</p>');
        }

        $.get(notificationsUrl)
            .done(function(response) {
                renderNotifications(response);
            })
            .fail(function() {
                list.html('<p class="text-center text-danger small mb-0">Unable to load notifications.</p>');
            });
    }

    function closeDropdown() {
        dropdown.removeClass('show');
    }

    bellWrapper.on('click', function(event) {
        event.stopPropagation();
        dropdown.toggleClass('show');

        if (dropdown.hasClass('show')) {
            loadNotifications(false);
        }
    });

    refreshBtn.on('click', function(event) {
        event.stopPropagation();
        loadNotifications(true);
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

    loadNotifications(false);
    setInterval(loadNotifications, 60000);
})(jQuery);
</script>
<?php endif; ?>

</body>
</html>
