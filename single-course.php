<?php
get_header();

if ( have_posts() ) :
    while ( have_posts() ) : the_post(); ?>
        
        <div class="course-container" style="max-width: 600px; margin: 0 auto;">
            <h1><?php the_title(); ?></h1>

            <?php
            // Получить преподавателя
            $teacher_id = get_post_meta(get_the_ID(), 'course_teacher', true);
            if ($teacher_id) {
                $teacher = get_user_by('ID', $teacher_id);
                echo '<p><strong>Преподаватель:</strong> ' . esc_html($teacher->display_name) . '</p>';
            }

            // Получить расписание
            $schedule = get_post_meta(get_the_ID(), 'course_schedule', true);
            if (!empty($schedule) && is_array($schedule)) {
                echo '<h3>Расписание:</h3>';
                echo '<ul>';
                foreach ($schedule as $day => $sessions) {
                    if (!empty($sessions)) {
                        echo '<li><strong>' . esc_html($day) . ':</strong><ul>';
                        foreach ($sessions as $session) {
                            if (!empty($session['start']) && !empty($session['end'])) {
                                echo '<li>' . esc_html($session['start']) . ' – ' . esc_html($session['end']) . '</li>';
                            }
                        }
                        echo '</ul></li>';
                    }
                }
                echo '</ul>';
            } else {
                echo '<p>Расписание пока не указано.</p>';
            }

            // Кнопка записи на курс
            if ( is_user_logged_in() ) {
                $current_user = wp_get_current_user();
                $course_id = get_the_ID();
                $enrolled_users = get_post_meta($course_id, 'course_students', true);
                if (!is_array($enrolled_users)) $enrolled_users = [];

                if (in_array($current_user->ID, $enrolled_users)) {
                    echo '<p><strong>✅ Вы уже записаны на этот курс.</strong></p>';
                } else {
                    ?>
                    <button id="enroll-button" data-course="<?php echo esc_attr($course_id); ?>">Записаться на курс</button>
                    <p id="enroll-status" style="margin-top: 10px;"></p>
                    <?php
                }
            } else {
                echo '<p>Пожалуйста, <a href="' . esc_url(wp_login_url(get_permalink())) . '">войдите</a>, чтобы записаться на курс.</p>';
            }
            ?>
        </div>

    <?php endwhile;
else :
    echo '<p>Курс не найден.</p>';
endif;

get_footer();
