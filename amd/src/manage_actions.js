define(['core/notification', 'core/modal_delete_cancel', 'core/modal_events', 'core/str'], function(
    Notification,
    ModalDeleteCancel,
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
                var name = link.getAttribute('data-confirm-name') || '';
                if (name) {
                    Str.get_strings([
                        {key: 'deletecurriculumtitle', component: 'block_programcurriculum'},
                        {key: 'deletecurriculumbody', component: 'block_programcurriculum', param: name}
                    ]).then(function(strings) {
                        return ModalDeleteCancel.create({
                            title: strings[0],
                            body: strings[1]
                        }).then(function(modal) {
                            var root = modal.getRoot();
                            var navigate = function() {
                                window.location.href = link.href;
                            };
                            root.on(ModalEvents.save, navigate);
                            root.on(ModalEvents.delete, navigate);
                            modal.show();
                            return modal;
                        });
                    }).catch(Notification.exception);
                    return;
                }
                ModalDeleteCancel.create({
                    title: message,
                    body: ''
                }).then(function(modal) {
                    var root = modal.getRoot();
                    var navigate = function() {
                        window.location.href = link.href;
                    };
                    root.on(ModalEvents.save, navigate);
                    root.on(ModalEvents.delete, navigate);
                    modal.show();
                    return modal;
                }).catch(Notification.exception);
            });
        });

        var openModalButton = document.querySelector('[data-open-curriculum-modal="1"]');
        var modalTitle = document.getElementById('programcurriculum-curriculum-modal-title');
        var form = document.getElementById('programcurriculum-curriculum-form');

        var setFormValue = function(name, value) {
            if (!form) {
                return;
            }
            var element = form.elements.namedItem(name);
            if (element) {
                element.value = value === null || value === undefined ? '' : value;
            }
        };

        var openModal = function() {
            if (openModalButton) {
                openModalButton.click();
            }
        };

        var addButton = document.querySelector('[data-action="add-curriculum"]');
        if (addButton && !addButton.dataset.modalBound) {
            addButton.dataset.modalBound = '1';
            addButton.addEventListener('click', function() {
                if (modalTitle) {
                    modalTitle.textContent = modalTitle.dataset.addTitle || modalTitle.textContent;
                }
                setFormValue('id', 0);
                setFormValue('name', '');
                setFormValue('externalcode', '');
                setFormValue('description', '');
            });
        }

        var editLinks = document.querySelectorAll('a[data-action="edit-curriculum"]');
        editLinks.forEach(function(link) {
            if (link.dataset.modalBound === '1') {
                return;
            }
            link.dataset.modalBound = '1';
            link.addEventListener('click', function(event) {
                event.preventDefault();
                if (modalTitle) {
                    modalTitle.textContent = modalTitle.dataset.editTitle || modalTitle.textContent;
                }
                setFormValue('id', link.dataset.editId || 0);
                setFormValue('name', link.dataset.editName || '');
                setFormValue('externalcode', link.dataset.editCode || '');
                setFormValue('description', link.dataset.editDescription || '');
                openModal();
            });
        });
    };

    return {
        init: function() {
            attachHandlers();
        }
    };
});
