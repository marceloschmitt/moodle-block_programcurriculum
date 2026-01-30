define([], function() {
    'use strict';

    var attachHandlers = function() {
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

        var container = document.querySelector('.programcurriculum-manage[data-validation-error="1"]');
        if (container && openModalButton) {
            openModal();
        }
    };

    return {
        init: function() {
            attachHandlers();
        }
    };
});
