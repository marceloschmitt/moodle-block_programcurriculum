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
 * @package
 * @copyright  2026 Marcelo Augusto Rauh Schmitt <marcelo.schmitt@poa.ifrs.edu.br>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['core/ajax', 'core/notification'], function(Ajax, Notification) {
    'use strict';

    var init = function() {
        var container = document.querySelector('.programcurriculum-progress');
        var modalTitle = document.getElementById('programcurriculum-moodle-modal-title');
        var modalBody = document.getElementById('programcurriculum-moodle-modal-body');
        var modalTrigger = document.getElementById('programcurriculum-moodle-modal-trigger');
        var clickableRows = document.querySelectorAll('.programcurriculum-row-clickable');

        var courseid = container && container.getAttribute('data-course-id');
        if (courseid) {
            var checkboxes = container.querySelectorAll('.programcurriculum-user-completion');
            checkboxes.forEach(function(cb) {
                cb.addEventListener('change', function() {
                    var externalcourseid = cb.getAttribute('data-external-course-id');
                    var completed = cb.checked;
                    var request = {
                        methodname: 'block_programcurriculum_toggle_user_completion',
                        args: {
                            courseid: parseInt(courseid, 10),
                            externalcourseid: parseInt(externalcourseid, 10),
                            completed: completed
                        }
                    };
                    Ajax.call([request])[0].then(function() {
                        window.location.reload();
                    }).catch(Notification.exception);
                });
            });
        }

        if (!modalTitle || !modalBody || !modalTrigger) {
            return;
        }

        var completedStr = (container && container.getAttribute('data-completed-str')) || 'Completed';
        var notcompletedStr = (container && container.getAttribute('data-notcompleted-str')) || 'Not completed';

        /**
         * Opens the Moodle courses modal for one external course row.
         *
         * @param {HTMLElement} row Clicked row element.
         */
        function openModal(row) {
                var externalName = row.getAttribute('data-external-name') || '';
                var moodlecoursesEncoded = row.getAttribute('data-moodlecourses') || '';
                var moodlecourses = [];
                try {
                    var moodlecoursesJson = moodlecoursesEncoded ? atob(moodlecoursesEncoded) : '[]';
                    moodlecourses = JSON.parse(moodlecoursesJson);
                } catch (err) {
                    moodlecourses = [];
                }

                modalTitle.textContent = externalName;

                var bodyHtml = '<ul class="list-unstyled mb-0">';
                moodlecourses.forEach(function(mc) {
                    var nameEscaped = (mc.name || '').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    var courseUrl = mc.url || '#';
                    bodyHtml += '<li class="py-2 border-bottom d-flex justify-content-between align-items-center">';
                    bodyHtml += '<a href="' + (courseUrl.replace(/"/g, '&quot;')) + '">' + nameEscaped + '</a>';
                    bodyHtml += '<span class="badge ' + (mc.completed ? 'bg-success' : 'bg-secondary') + '">';
                    bodyHtml += mc.completed ? completedStr : notcompletedStr;
                    bodyHtml += '</span></li>';
                });
                bodyHtml += '</ul>';
                modalBody.innerHTML = bodyHtml;

                modalTrigger.click();
        }

        if (clickableRows.length) {
            clickableRows.forEach(function(row) {
                row.addEventListener('click', function(e) {
                    if (!e.target.closest('.programcurriculum-user-completion, label')) {
                        openModal(row);
                    }
                });
                row.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        if (!e.target.closest('.programcurriculum-user-completion, label')) {
                            e.preventDefault();
                            openModal(row);
                        }
                    }
                });
            });
        }
    };

    return {
        init: init
    };
});
