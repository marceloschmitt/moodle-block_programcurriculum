define(['core/modal'], function(Modal) {
    'use strict';

    var init = function() {
        var container = document.querySelector('.programcurriculum-progress');
        var modalBody = document.getElementById('programcurriculum-moodle-modal-body');
        var links = document.querySelectorAll('.programcurriculum-external-link');

        if (!container || !links.length) {
            return;
        }

        var completedStr = container.getAttribute('data-completed-str') || 'Completed';
        var notcompletedStr = container.getAttribute('data-notcompleted-str') || 'Not completed';

        links.forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                var externalName = link.getAttribute('data-external-name') || '';
                var moodlecoursesJson = link.getAttribute('data-moodlecourses') || '[]';
                var moodlecourses = [];
                try {
                    moodlecourses = JSON.parse(moodlecoursesJson);
                } catch (err) {
                    moodlecourses = [];
                }

                var bodyHtml = '<ul class="list-unstyled mb-0">';
                moodlecourses.forEach(function(mc) {
                    bodyHtml += '<li class="py-2 border-bottom d-flex justify-content-between align-items-center">';
                    bodyHtml += '<span>' + (mc.name || '').replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</span>';
                    bodyHtml += '<span class="badge ' + (mc.completed ? 'bg-success' : 'bg-secondary') + '">';
                    bodyHtml += mc.completed ? completedStr : notcompletedStr;
                    bodyHtml += '</span></li>';
                });
                bodyHtml += '</ul>';

                Modal.create({
                    title: externalName,
                    body: bodyHtml,
                    footer: '',
                    show: true,
                    removeOnClose: true
                });
            });
        });
    };

    return {
        init: init
    };
});
