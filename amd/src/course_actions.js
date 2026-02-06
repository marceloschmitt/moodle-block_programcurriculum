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
        if (container && !container.dataset.moveBound) {
            container.dataset.moveBound = '1';
            var curriculumId = parseInt(container.getAttribute('data-curriculum-id') || '0', 10);
            var sesskey = container.getAttribute('data-sesskey') || '';
            var placeholders = [];

            var exitMoveMode = function() {
                placeholders.forEach(function(p) {
                    if (p.parentNode) {
                        p.parentNode.removeChild(p);
                    }
                });
                placeholders = [];
                container.classList.remove('programcurriculum-move-mode');
                var cancelBar = container.querySelector('.programcurriculum-move-cancel');
                if (cancelBar) {
                    cancelBar.remove();
                }
            };

            var enterMoveMode = function(courseId, moveBaseUrl, courseName) {
                exitMoveMode();
                container.classList.add('programcurriculum-move-mode');

                var cancelBar = document.createElement('div');
                cancelBar.className = 'alert alert-info programcurriculum-move-cancel d-flex align-items-center justify-content-between';
                var hintSpan = document.createElement('span');
                var cancelBtn = document.createElement('button');
                cancelBtn.type = 'button';
                cancelBtn.className = 'btn btn-sm btn-outline-secondary';
                cancelBtn.addEventListener('click', exitMoveMode);
                Str.get_string('movepositionclick', 'block_programcurriculum').then(function(s) {
                    hintSpan.textContent = s;
                }).catch(function() {
                    hintSpan.textContent = 'Click on a dashed line to move to that position.';
                });
                Str.get_string('cancel', 'moodle').then(function(s) {
                    cancelBtn.textContent = s;
                }).catch(function() {
                    cancelBtn.textContent = 'Cancel';
                });
                cancelBar.appendChild(hintSpan);
                cancelBar.appendChild(cancelBtn);
                var termsContainer = container.querySelector('.mb-4');
                if (termsContainer) {
                    container.insertBefore(cancelBar, termsContainer);
                } else {
                    container.appendChild(cancelBar);
                }

                var termSections = Array.from(container.querySelectorAll('.programcurriculum-term-section'));
                var globalPosition = 1;
                var createPlaceholder = function(pos) {
                    var ph = document.createElement('li');
                    ph.className = 'programcurriculum-move-placeholder';
                    ph.setAttribute('data-position', String(pos));
                    ph.setAttribute('role', 'button');
                    ph.setAttribute('tabindex', '0');
                    ph.setAttribute('aria-label', 'Mover para posição ' + pos);
                    ph.addEventListener('click', function() {
                        var url = moveBaseUrl + (moveBaseUrl.indexOf('?') >= 0 ? '&' : '?') + 'position=' + pos;
                        window.location.href = url;
                    });
                    ph.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            ph.click();
                        }
                    });
                    return ph;
                };
                termSections.forEach(function(section) {
                    var list = section.querySelector('.programcurriculum-term-list');
                    var items = section.querySelectorAll('.programcurriculum-course-item');
                    if (items.length === 0) {
                        var emptyPlaceholder = createPlaceholder(globalPosition);
                        list.insertBefore(emptyPlaceholder, list.firstChild);
                        placeholders.push(emptyPlaceholder);
                        globalPosition += 1;
                    } else {
                        items.forEach(function(item) {
                            var ph = createPlaceholder(globalPosition);
                            item.parentNode.insertBefore(ph, item);
                            placeholders.push(ph);
                            globalPosition += 1;
                        });
                        var lastPh = createPlaceholder(globalPosition);
                        items[items.length - 1].parentNode.appendChild(lastPh);
                        placeholders.push(lastPh);
                        globalPosition += 1;
                    }
                });
            };

            container.addEventListener('click', function(e) {
                var trigger = e.target.closest('.programcurriculum-move-trigger');
                if (trigger) {
                    e.preventDefault();
                    e.stopPropagation();
                    var courseId = trigger.getAttribute('data-move-course-id');
                    var moveUrl = trigger.getAttribute('data-move-target') || trigger.getAttribute('data-move-url') || '';
                    var courseName = trigger.getAttribute('data-move-name') || '';
                    if (courseId && moveUrl) {
                        enterMoveMode(courseId, moveUrl, courseName);
                    }
                }
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
