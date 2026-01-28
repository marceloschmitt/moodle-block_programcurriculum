define(['core/notification', 'core/modal_save_cancel', 'core/modal_delete_cancel', 'core/modal_events', 'core/str'], function(
    Notification,
    ModalSaveCancel,
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
                        {key: 'deletemappingtitle', component: 'block_programcurriculum'},
                        {key: 'deletemappingbody', component: 'block_programcurriculum', param: name}
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

        var mappingModalButton = document.querySelector('[data-open-mapping-modal="1"]');
        var mappingModalTitle = document.getElementById('programcurriculum-mapping-modal-title');
        var mappingForm = document.getElementById('programcurriculum-mapping-form');

        var setMappingValue = function(name, value) {
            if (!mappingForm) {
                return;
            }
            var element = mappingForm.elements.namedItem(name);
            if (element) {
                element.value = value;
            }
        };

        var setMappingCourseFrozen = function(freeze) {
            if (!mappingForm) {
                return;
            }
            var element = mappingForm.elements.namedItem('moodlecourseid');
            if (element) {
                element.disabled = !!freeze;
            }
        };

        var openMappingModal = function() {
            if (mappingModalButton) {
                mappingModalButton.click();
            }
        };

        var addMappingButton = document.querySelector('[data-action="add-mapping"]');
        if (addMappingButton && !addMappingButton.dataset.modalBound) {
            addMappingButton.dataset.modalBound = '1';
            addMappingButton.addEventListener('click', function() {
                if (mappingModalTitle) {
                    mappingModalTitle.textContent = mappingModalTitle.dataset.addTitle || mappingModalTitle.textContent;
                }
                setMappingValue('id', 0);
                setMappingValue('moodlecourseid', '');
                setMappingValue('required', 1);
                setMappingValue('courseid', addMappingButton.dataset.courseId || 0);
                setMappingCourseFrozen(false);
            });
        }

        var editMappingLinks = document.querySelectorAll('a[data-action="edit-mapping"]');
        editMappingLinks.forEach(function(link) {
            if (link.dataset.modalBound === '1') {
                return;
            }
            link.dataset.modalBound = '1';
            link.addEventListener('click', function(event) {
                event.preventDefault();
                if (mappingModalTitle) {
                    mappingModalTitle.textContent = mappingModalTitle.dataset.editTitle || mappingModalTitle.textContent;
                }
                setMappingValue('id', link.dataset.editId || 0);
                setMappingValue('moodlecourseid', link.dataset.editMoodlecourseid || '');
                setMappingValue('required', link.dataset.editRequired || 0);
                setMappingValue('courseid', link.dataset.editCourse || 0);
                setMappingCourseFrozen(true);
                openMappingModal();
            });
        });
    };

    return {
        init: function() {
            attachHandlers();
        }
    };
});
