define(['core/ajax', 'core/notification', 'core/modal_delete_cancel', 'core/modal_events', 'core/str'], function(
    Ajax,
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
                        {key: 'deletecoursetitle', component: 'block_programcurriculum'},
                        {key: 'deletecoursebody', component: 'block_programcurriculum', param: name}
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

        var container = document.querySelector('.programcurriculum-course[data-curriculum-id]');
        if (container && !container.dataset.dragBound) {
            container.dataset.dragBound = '1';
            var curriculumId = parseInt(container.getAttribute('data-curriculum-id') || '0', 10);
            var draggedItem = null;
            var dragOverItem = null;

            var getOrderedItems = function() {
                return Array.from(container.querySelectorAll('.programcurriculum-course-item'));
            };

            var getNewPosition = function(targetItem) {
                var items = getOrderedItems();
                var idx = items.indexOf(targetItem);
                return idx >= 0 ? idx + 1 : 1;
            };

            container.addEventListener('dragstart', function(e) {
                var item = e.target.closest('.programcurriculum-course-item');
                if (item) {
                    draggedItem = item;
                    item.classList.add('programcurriculum-dragging');
                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.setData('text/plain', item.getAttribute('data-course-id') || '');
                    e.dataTransfer.setData('application/json', JSON.stringify({
                        courseId: item.getAttribute('data-course-id'),
                        position: item.getAttribute('data-position')
                    }));
                }
            });

            container.addEventListener('dragend', function(e) {
                if (draggedItem) {
                    draggedItem.classList.remove('programcurriculum-dragging');
                    container.querySelectorAll('.programcurriculum-drag-over').forEach(function(el) {
                        el.classList.remove('programcurriculum-drag-over');
                    });
                    draggedItem = null;
                    dragOverItem = null;
                }
            });

            container.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                var item = e.target.closest('.programcurriculum-course-item');
                if (item && item !== draggedItem) {
                    dragOverItem = item;
                    item.classList.add('programcurriculum-drag-over');
                }
            });

            container.addEventListener('dragleave', function(e) {
                var item = e.target.closest('.programcurriculum-course-item');
                if (item) {
                    item.classList.remove('programcurriculum-drag-over');
                }
            });

            container.addEventListener('drop', function(e) {
                e.preventDefault();
                var item = e.target.closest('.programcurriculum-course-item');
                if (item) {
                    item.classList.remove('programcurriculum-drag-over');
                }
                if (!draggedItem || !item || item === draggedItem || !curriculumId) {
                    return;
                }
                var courseId = parseInt(draggedItem.getAttribute('data-course-id') || '0', 10);
                var newPosition = getNewPosition(item);

                var request = {
                    methodname: 'block_programcurriculum_reorder_courses',
                    args: {
                        curriculumid: curriculumId,
                        courseid: courseId,
                        newposition: newPosition
                    }
                };
                Ajax.call([request])[0]
                    .done(function(response) {
                        if (response && response.success) {
                            window.location.reload();
                        }
                    })
                    .fail(Notification.exception);
            });
        }

        var openModalButton = document.querySelector('[data-open-course-modal="1"]');
        var modalTitle = document.getElementById('programcurriculum-course-modal-title');
        var form = document.getElementById('programcurriculum-course-form');

        var setFormValue = function(name, value) {
            if (!form) {
                return;
            }
            var element = form.elements.namedItem(name);
            if (element) {
                element.value = value;
            }
        };

        var openModal = function() {
            if (openModalButton) {
                openModalButton.click();
            }
        };

        var addButton = document.querySelector('[data-action="add-course"]');
        if (addButton && !addButton.dataset.modalBound) {
            addButton.dataset.modalBound = '1';
            addButton.addEventListener('click', function() {
                if (modalTitle) {
                    modalTitle.textContent = modalTitle.dataset.addTitle || modalTitle.textContent;
                }
                setFormValue('id', 0);
                setFormValue('name', '');
                setFormValue('externalcode', '');
                setFormValue('sortorder', 0);
                setFormValue('term', 1);
            });
        }

        var editLinks = document.querySelectorAll('a[data-action="edit-course"]');
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
                setFormValue('sortorder', link.dataset.editSortorder || 0);
                setFormValue('term', link.dataset.editTerm || 1);
                setFormValue('curriculumid', link.dataset.editCurriculum || 0);
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
