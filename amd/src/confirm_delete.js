define(['core/notification', 'core/modal_factory', 'core/modal_events', 'core/templates', 'core/str'], function(
    Notification,
    ModalFactory,
    ModalEvents,
    Templates,
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
                    {key: 'movepositionhelp', component: 'block_programcurriculum', param: max}
                ]).then(function(strings) {
                    return Templates.render('block_programcurriculum/move_prompt', {
                        current: current,
                        max: max,
                        helptext: strings[4]
                    }).then(function(html, js) {
                        return ModalFactory.create({
                            title: strings[0],
                            body: html,
                            type: ModalFactory.types.SAVE_CANCEL,
                            buttons: {
                                save: strings[1],
                                cancel: strings[2]
                            }
                        }).then(function(modal) {
                            Templates.runTemplateJS(js);
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
