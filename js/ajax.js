document.addEventListener('DOMContentLoaded', function () {
    function ajaxAction(el, action) {
        const courseId = el.dataset.course;
        const day = el.dataset.day;
        const lesson = el.dataset.lesson;

        fetch(ajax_object.ajax_url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                action: action,
                course_id: courseId,
                day: day,
                lesson_number: lesson,
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) location.reload();
            else alert(data.data || 'Ошибка');
        });
    }

    document.querySelectorAll('.signup-btn').forEach(btn => {
        btn.addEventListener('click', () => ajaxAction(btn, 'signup_for_lesson'));
    });

    document.querySelectorAll('.unsubscribe-btn').forEach(btn => {
        btn.addEventListener('click', () => ajaxAction(btn, 'unsubscribe_from_lesson'));
    });
});
