define(['core/notification'], function(Notification) {
    'use strict';

    var attachHandlers = function() {
        var links = document.querySelectorAll('a[data-confirm-message]');
        links.forEach(function(link) {
            if (link.dataset.confirmBound === '1') {
                return;
            }
            link.dataset.confirmBound = '1';
            link.addEventListener('click', function(event) {
                event.preventDefault();
                var message = link.getAttribute('data-confirm-message') || '';
                Notification.confirm(
                    '',
                    message,
                    '',
                    null,
                    function() {
                        window.location.href = link.href;
                    }
                );
            });
        });
    };

    return {
        init: function() {
            attachHandlers();
        }
    };
});
