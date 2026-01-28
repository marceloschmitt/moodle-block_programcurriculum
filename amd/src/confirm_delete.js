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

        var moveLinks = document.querySelectorAll('a[data-move-url]');
        moveLinks.forEach(function(link) {
            if (link.dataset.moveBound === '1') {
                return;
            }
            link.dataset.moveBound = '1';
            link.addEventListener('click', function(event) {
                event.preventDefault();
                var max = parseInt(link.getAttribute('data-max-position') || '1', 10);
                var current = parseInt(link.getAttribute('data-current-position') || '1', 10);
                var promptMessage = link.getAttribute('data-move-prompt') || '';
                var invalidMessage = link.getAttribute('data-move-invalid') || '';
                var input = window.prompt(promptMessage, String(current));
                if (input === null) {
                    return;
                }
                var position = parseInt(input, 10);
                if (isNaN(position) || position < 1 || position > max) {
                    if (invalidMessage) {
                        Notification.alert('', invalidMessage, '');
                    }
                    return;
                }
                var url = link.getAttribute('data-move-url') || link.href;
                var separator = url.indexOf('?') === -1 ? '?' : '&';
                window.location.href = url + separator + 'position=' + position;
            });
        });
    };

    return {
        init: function() {
            attachHandlers();
        }
    };
});
