define([], function() {
    'use strict';

    var init = function() {
        var container = document.querySelector('.programcurriculum-progress');
        var modalTitle = document.getElementById('programcurriculum-moodle-modal-title');
        var modalBody = document.getElementById('programcurriculum-moodle-modal-body');
        var modalTrigger = document.getElementById('programcurriculum-moodle-modal-trigger');
        var buttons = document.querySelectorAll('.programcurriculum-moodle-btn');

        if (!modalTitle || !modalBody || !modalTrigger || !buttons.length) {
            return;
        }

        var completedStr = (container && container.getAttribute('data-completed-str')) || 'Completed';
        var notcompletedStr = (container && container.getAttribute('data-notcompleted-str')) || 'Not completed';

        buttons.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                var externalName = btn.getAttribute('data-external-name') || '';
                var moodlecoursesEncoded = btn.getAttribute('data-moodlecourses') || '';
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
                    var url = mc.url || '#';
                    bodyHtml += '<li class="py-2 border-bottom d-flex justify-content-between align-items-center">';
                    bodyHtml += '<a href="' + (url.replace(/"/g, '&quot;')) + '">' + nameEscaped + '</a>';
                    bodyHtml += '<span class="badge ' + (mc.completed ? 'bg-success' : 'bg-secondary') + '">';
                    bodyHtml += mc.completed ? completedStr : notcompletedStr;
                    bodyHtml += '</span></li>';
                });
                bodyHtml += '</ul>';
                modalBody.innerHTML = bodyHtml;

                modalTrigger.click();
            });
        });
    };

    return {
        init: init
    };
});
