document.addEventListener("DOMContentLoaded", function () {
  function ajaxAction(el, action) {
    if (!ajax_object.is_logged_in) {
      alert("Пожалуйста, войдите в систему, чтобы записаться на курс.");
      window.location.href =
        "/wp-login.php?redirect_to=" + encodeURIComponent(window.location.href);
      return;
    }

    const courseId = el.dataset.course;
    const day = el.dataset.day;
    const lesson_number = el.dataset.lesson;

    fetch(ajax_object.ajax_url, {
      method: "POST",
      credentials: "same-origin",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({
        action: action,
        course_id: courseId,
        day: day,
        lesson_number: lesson_number,
        nonce: ajax_object.nonce,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          location.reload();
        } else {
          if (data.data && data.data.includes("войти")) {
            if (confirm("Для записи нужно войти. Перейти на страницу входа?")) {
              window.location.href =
                "/wp-login.php?redirect_to=" +
                encodeURIComponent(window.location.href);
            }
          } else {
            alert(data.data || "Ошибка");
          }
        }
      });
  }

  document.querySelectorAll(".signup-btn").forEach((btn) => {
    btn.addEventListener("click", () => ajaxAction(btn, "signup_for_lesson"));
  });

  document.querySelectorAll(".unsubscribe-btn").forEach((btn) => {
    btn.addEventListener("click", () =>
      ajaxAction(btn, "unsubscribe_from_lesson")
    );
  });
});
