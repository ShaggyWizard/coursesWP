<?php
get_header();
$current_user_id = get_current_user_id();

$days_of_week = [
    0 => 'Понедельник',
    1 => 'Вторник',
    2 => 'Среда',
    3 => 'Четверг',
    4 => 'Пятница',
    5 => 'Суббота',
    6 => 'Воскресенье',
];

$args = [
    'post_type' => 'course',
    'posts_per_page' => -1,
];

$query = new WP_Query($args);

if ($query->have_posts()):
    while ($query->have_posts()):
        $query->the_post();
        $course_id = get_the_ID();
        $title = get_the_title();
        $teacher_id = get_post_meta($course_id, '_course_teacher', true);
        $schedule = get_post_meta($course_id, '_course_schedule_structured', true);
        $signups = get_post_meta($course_id, '_course_signups', true) ?: [];

        // Определяем макс. количество занятий в любом дне
        $max_lessons = 0;
        for ($day = 0; $day <= 6; $day++) {
            if (!empty($schedule[$day]) && count($schedule[$day]) > $max_lessons) {
                $max_lessons = count($schedule[$day]);
            }
        }
        ?>
        
        <h2><?= esc_html($title) ?></h2>
        <p><strong>Преподаватель:</strong> <?= esc_html(get_the_author_meta('display_name', $teacher_id)) ?></p>

        <table class="course-schedule">
            <thead>
                <tr>
                    <th>Занятие</th>
                    <?php foreach ($days_of_week as $day_name): ?>
                        <th><?= esc_html($day_name) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php for ($lesson_index = 0; $lesson_index < $max_lessons; $lesson_index++): ?>
                    <tr>
                        <td>Занятие #<?= $lesson_index + 1 ?></td>
                        <?php for ($day = 0; $day <= 6; $day++): ?>
                            <td>
                                <?php if (!empty($schedule[$day][$lesson_index])): 
                                    $lesson = $schedule[$day][$lesson_index];
                                    $start = esc_html($lesson['start'] ?? '');
                                    $end = esc_html($lesson['end'] ?? '');

                                    $user_ids = $signups[$day][$lesson_index] ?? [];
                                    $names = array_map(function ($uid) {
                                        $user = get_userdata($uid);
                                        return $user ? esc_html($user->display_name) : 'Гость';
                                    }, $user_ids);

                                    $is_signed_up = in_array($current_user_id, $user_ids);

                                    $action_class = $is_signed_up ? 'unsubscribe-btn' : 'signup-btn';
                                    $action_text = $is_signed_up ? 'Отписаться' : 'Записаться';
                                ?>
                                    <div><strong>Время:</strong> <?= $start ?> — <?= $end ?></div>
                                    <div>
                                        <button 
                                            class="<?= $action_class ?>" 
                                            data-course="<?= $course_id ?>" 
                                            data-day="<?= $day ?>" 
                                            data-lesson="<?= $lesson_index ?>">
                                            <?= $action_text ?>
                                        </button>
                                    </div>
                                    <div><small>Записаны: <?= !empty($names) ? implode(', ', $names) : 'нет' ?></small></div>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        <?php endfor; ?>
                    </tr>
                <?php endfor; ?>
            </tbody>
        </table>
        <hr>

    <?php
    endwhile;
endif;

wp_reset_postdata();
get_footer();
