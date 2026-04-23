/**
 * This file is part of Moodle - http://moodle.org/
 *
 * Moodle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Moodle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    block_programcurriculum
 * @copyright  2026 Marcelo Augusto Rauh Schmitt <marcelo.schmitt@poa.ifrs.edu.br>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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
            });
        }
    };

    return {
        init: function() {
            attachHandlers();
        }
    };
});
