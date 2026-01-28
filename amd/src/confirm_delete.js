define(['core/notification', 'core/modal_factory', 'core/modal_events', 'core/str'], function(
    Notification,
    ModalFactory,
    ModalEvents,
    Str
) {
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
                var url = link.getAttribute('data-move-url') || link.href;

                Str.get_strings([
                    {key: 'movemodaltitle', component: 'block_programcurriculum'},
                    {key: 'move', component: 'block_programcurriculum'},
                    {key: 'cancel', component: 'moodle'},
                    {key: 'movepositioninvalid', component: 'block_programcurriculum'},
                    {key: 'movepositionhelp', component: 'block_programcurriculum', param: max},
                    {key: 'moveto', component: 'block_programcurriculum'}
                ]).then(function(strings) {
                    var body = '<div class="block-programcurriculum-move">' +
                        '<label class="form-label" for="block-programcurriculum-move-input">' + strings[5] + '</label>' +
                        '<input id="block-programcurriculum-move-input" type="number" min="1" max="' + max +
                        '" value="' + current + '" class="form-control">' +
                        '<div class="form-text">' + strings[4] + '</div>' +
                        '</div>';
                    return ModalFactory.create({
                        title: strings[0],
                        body: body,
                        type: ModalFactory.types.SAVE_CANCEL,
                        buttons: {
                            save: strings[1],
                            cancel: strings[2]
                        }
                    }).then(function(modal) {
                        modal.getRoot().on(ModalEvents.save, function(e) {
                            var input = modal.getRoot().find('#block-programcurriculum-move-input').val();
                            var position = parseInt(input, 10);
                            if (isNaN(position) || position < 1 || position > max) {
                                e.preventDefault();
                                Notification.alert('', strings[3], '');
                                return;
                            }
                            var separator = url.indexOf('?') === -1 ? '?' : '&';
                            window.location.href = url + separator + 'position=' + position;
                        });
                        modal.show();
                        return modal;
                    });
                }).catch(Notification.exception);
            });
        });
    };

    return {
        init: function() {
            attachHandlers();
        }
    };
});
