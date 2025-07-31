<?php
add_action('after_setup_theme', 'blankslate_setup');
function blankslate_setup()
{
    load_theme_textdomain('blankslate', get_template_directory() . '/languages');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');
    add_theme_support('automatic-feed-links');
    add_theme_support('html5', array('search-form', 'navigation-widgets'));
    add_theme_support('appearance-tools');
    add_theme_support('woocommerce');
    global $content_width;
    if (!isset($content_width)) {
        $content_width = 1920;
    }
    register_nav_menus(array('main-menu' => esc_html__('Main Menu', 'blankslate')));
}
add_action('admin_notices', 'blankslate_notice');
function blankslate_notice()
{
    $user_id = get_current_user_id();
    $admin_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $param = (count($_GET)) ? '&' : '?';
    if (!get_user_meta($user_id, 'blankslate_notice_dismissed_12') && current_user_can('manage_options'))
        echo '<div class="notice notice-info"><p><a href="' . esc_url($admin_url), esc_html($param) . 'dismiss" class="alignright" style="text-decoration:none"><big>' . esc_html__('Ⓧ', 'blankslate') . '</big></a>' . wp_kses_post(__('<big><strong>🏆 Thank you for using BlankSlate!</strong></big>', 'blankslate')) . '<p>' . esc_html__('Powering over 10k websites! Buy me a sandwich! 🥪', 'blankslate') . '</p><a href="https://github.com/webguyio/blankslate/issues/57" class="button-primary" target="_blank"><strong>' . esc_html__('How do you use BlankSlate?', 'blankslate') . '</strong></a> <a href="https://opencollective.com/blankslate" class="button-primary" style="background-color:green;border-color:green" target="_blank"><strong>' . esc_html__('Donate', 'blankslate') . '</strong></a> <a href="https://wordpress.org/support/theme/blankslate/reviews/#new-post" class="button-primary" style="background-color:purple;border-color:purple" target="_blank"><strong>' . esc_html__('Review', 'blankslate') . '</strong></a> <a href="https://github.com/webguyio/blankslate/issues" class="button-primary" style="background-color:orange;border-color:orange" target="_blank"><strong>' . esc_html__('Support', 'blankslate') . '</strong></a></p></div>';
}
add_action('admin_init', 'blankslate_notice_dismissed');
function blankslate_notice_dismissed()
{
    $user_id = get_current_user_id();
    if (isset($_GET['dismiss']))
        add_user_meta($user_id, 'blankslate_notice_dismissed_12', 'true', true);
}
add_action('wp_enqueue_scripts', 'blankslate_enqueue');
function blankslate_enqueue()
{
    wp_enqueue_style('blankslate-style', get_stylesheet_uri());
    wp_enqueue_script('jquery');
}
add_action('wp_footer', 'blankslate_footer');
function blankslate_footer()
{
    ?>
    <script>
        jQuery(document).ready(function ($) {
            var deviceAgent = navigator.userAgent.toLowerCase();
            if (deviceAgent.match(/(iphone|ipod|ipad)/)) {
                $("html").addClass("ios");
                $("html").addClass("mobile");
            }
            if (deviceAgent.match(/(Android)/)) {
                $("html").addClass("android");
                $("html").addClass("mobile");
            }
            if (navigator.userAgent.search("MSIE") >= 0) {
                $("html").addClass("ie");
            }
            else if (navigator.userAgent.search("Chrome") >= 0) {
                $("html").addClass("chrome");
            }
            else if (navigator.userAgent.search("Firefox") >= 0) {
                $("html").addClass("firefox");
            }
            else if (navigator.userAgent.search("Safari") >= 0 && navigator.userAgent.search("Chrome") < 0) {
                $("html").addClass("safari");
            }
            else if (navigator.userAgent.search("Opera") >= 0) {
                $("html").addClass("opera");
            }
        });
    </script>
    <?php
}
add_filter('document_title_separator', 'blankslate_document_title_separator');
function blankslate_document_title_separator($sep)
{
    $sep = esc_html('|');
    return $sep;
}
add_filter('the_title', 'blankslate_title');
function blankslate_title($title)
{
    if ($title == '') {
        return esc_html('...');
    } else {
        return wp_kses_post($title);
    }
}
function blankslate_schema_type()
{
    $schema = 'https://schema.org/';
    if (is_single()) {
        $type = "Article";
    } elseif (is_author()) {
        $type = 'ProfilePage';
    } elseif (is_search()) {
        $type = 'SearchResultsPage';
    } else {
        $type = 'WebPage';
    }
    echo 'itemscope itemtype="' . esc_url($schema) . esc_attr($type) . '"';
}
add_filter('nav_menu_link_attributes', 'blankslate_schema_url', 10);
function blankslate_schema_url($atts)
{
    $atts['itemprop'] = 'url';
    return $atts;
}
if (!function_exists('blankslate_wp_body_open')) {
    function blankslate_wp_body_open()
    {
        do_action('wp_body_open');
    }
}
add_action('wp_body_open', 'blankslate_skip_link', 5);
function blankslate_skip_link()
{
    echo '<a href="#content" class="skip-link screen-reader-text">' . esc_html__('Skip to the content', 'blankslate') . '</a>';
}
add_filter('the_content_more_link', 'blankslate_read_more_link');
function blankslate_read_more_link()
{
    if (!is_admin()) {
        return ' <a href="' . esc_url(get_permalink()) . '" class="more-link">' . sprintf(__('...%s', 'blankslate'), '<span class="screen-reader-text">  ' . esc_html(get_the_title()) . '</span>') . '</a>';
    }
}
add_filter('excerpt_more', 'blankslate_excerpt_read_more_link');
function blankslate_excerpt_read_more_link($more)
{
    if (!is_admin()) {
        global $post;
        return ' <a href="' . esc_url(get_permalink($post->ID)) . '" class="more-link">' . sprintf(__('...%s', 'blankslate'), '<span class="screen-reader-text">  ' . esc_html(get_the_title()) . '</span>') . '</a>';
    }
}
add_filter('big_image_size_threshold', '__return_false');
add_filter('intermediate_image_sizes_advanced', 'blankslate_image_insert_override');
function blankslate_image_insert_override($sizes)
{
    unset($sizes['medium_large']);
    unset($sizes['1536x1536']);
    unset($sizes['2048x2048']);
    return $sizes;
}
add_action('widgets_init', 'blankslate_widgets_init');
function blankslate_widgets_init()
{
    register_sidebar(array(
        'name' => esc_html__('Sidebar Widget Area', 'blankslate'),
        'id' => 'primary-widget-area',
        'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));
}
add_action('wp_head', 'blankslate_pingback_header');
function blankslate_pingback_header()
{
    if (is_singular() && pings_open()) {
        printf('<link rel="pingback" href="%s">' . "\n", esc_url(get_bloginfo('pingback_url')));
    }
}
add_action('comment_form_before', 'blankslate_enqueue_comment_reply_script');
function blankslate_enqueue_comment_reply_script()
{
    if (get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
function blankslate_custom_pings($comment)
{
    ?>
    <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>"><?php echo esc_url(comment_author_link()); ?>
    </li>
    <?php
}
add_filter('get_comments_number', 'blankslate_comment_count', 0);
function blankslate_comment_count($count)
{
    if (!is_admin()) {
        global $id;
        $get_comments = get_comments('status=approve&post_id=' . $id);
        $comments_by_type = separate_comments($get_comments);
        return count($comments_by_type['comment']);
    } else {
        return $count;
    }
}

add_action('init', 'register_post_types');

function register_post_types()
{
    register_post_type('course', [
        'labels' => array(
            'name' => 'Курсы',
            'singular_name' => 'Курс',
            'add_new' => 'Добавить новый',
            'add_new_item' => 'Добавить новый курс',
            'edit_item' => 'Редактировать курс',
            'new_item' => 'Новый курс',
            'view_item' => 'Посмотреть курс',
            'search_items' => 'Поиск курсов',
            'not_found' => 'Курсы не найдены',
            'not_found_in_trash' => 'В корзине нет курсов',
            'all_items' => 'Все курсы',
        ),
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => array('title', 'editor', 'custom-fields'),
        'menu_position' => 5,
        'menu_icon' => 'dashicons-welcome-learn-more',
        'rewrite' => array('slug' => 'courses'),
    ]);
}
function add_course_meta_boxes()
{
    add_meta_box(
        'course_details',
        'Детали курса',
        'render_course_meta_box',
        'course',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'add_course_meta_boxes');

function render_course_meta_box($post)
{
    $saved = get_post_meta($post->ID, '_course_schedule_structured', true);
    $teacher_id = get_post_meta($post->ID, '_course_teacher', true);
    $admins = get_users(array('role' => 'administrator'));

    $week_days = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'];

    wp_nonce_field('save_course_meta_nonce', 'course_meta_nonce');
    ?>
    <style>
        .day-group {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 15px;
        }

        .time-entry {
            margin-bottom: 5px;
        }
    </style>

    <p>
        <label for="course_teacher"><strong>Преподаватель <span style="color:red">*</span></strong></label><br>
        <select name="course_teacher" id="course_teacher" style="width:100%;" required>
            <option value="">-- Выберите преподавателя --</option>
            <?php foreach ($admins as $admin): ?>
                <option value="<?php echo esc_attr($admin->ID); ?>" <?php selected($teacher_id, $admin->ID); ?>>
                    <?php echo esc_html($admin->display_name); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>

    <?php for ($i = 0; $i <= 6; $i++): ?>
        <?php $entries = isset($saved[$i]) ? $saved[$i] : []; ?>
        <div class="day-group">
            <strong><?php echo $week_days[$i]; ?></strong>
            <div class="time-entries" data-day="<?php echo $i; ?>">
                <?php foreach ($entries as $index => $entry): ?>
                    <div class="time-entry">
                        <input type="time" name="schedule[<?php echo $i; ?>][<?php echo $index; ?>][start]"
                            value="<?php echo esc_attr($entry['start']); ?>" required />
                        <input type="time" name="schedule[<?php echo $i; ?>][<?php echo $index; ?>][end]"
                            value="<?php echo esc_attr($entry['end']); ?>" required />
                        <button type="button" class="remove-time button">–</button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="add-time button" data-day="<?php echo $i; ?>">+ Добавить время</button>
        </div>
    <?php endfor; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.add-time').forEach(button => {
                button.addEventListener('click', () => {
                    const day = button.dataset.day;
                    const wrapper = document.querySelector('.time-entries[data-day="' + day + '"]');
                    const index = wrapper.children.length;

                    const div = document.createElement('div');
                    div.className = 'time-entry';
                    div.innerHTML = `
                        <input type="time" name="schedule[${day}][${index}][start]" required />
                        <input type="time" name="schedule[${day}][${index}][end]" required />
                        <button type="button" class="remove-time button">–</button>
                    `;
                    wrapper.appendChild(div);
                });
            });

            document.addEventListener('click', e => {
                if (e.target.classList.contains('remove-time')) {
                    e.preventDefault();
                    e.target.closest('.time-entry').remove();
                }
            });

            document.querySelector('form#post').addEventListener('submit', e => {
                let valid = true;
                document.querySelectorAll('.time-entry').forEach(entry => {
                    const start = entry.querySelector('input[name*="[start]"]');
                    const end = entry.querySelector('input[name*="[end]"]');
                    if (!start.value || !end.value) {
                        valid = false;
                    }
                });
                if (!valid) {
                    e.preventDefault();
                    alert('Пожалуйста, заполните все поля начала и конца времени.');
                }
            });
        });
    </script>
    <?php
}

function save_course_meta($post_id)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;
    if (!isset($_POST['course_meta_nonce']) || !wp_verify_nonce($_POST['course_meta_nonce'], 'save_course_meta_nonce'))
        return;
    if (!current_user_can('edit_post', $post_id))
        return;

    // Сохраняем расписание
    if (isset($_POST['schedule']) && is_array($_POST['schedule'])) {
        $clean_schedule = [];

        foreach ($_POST['schedule'] as $day => $entries) {
            foreach ($entries as $entry) {
                $start = sanitize_text_field($entry['start'] ?? '');
                $end = sanitize_text_field($entry['end'] ?? '');
                if ($start && $end) {
                    $clean_schedule[$day][] = [
                        'start' => $start,
                        'end' => $end
                    ];
                }
            }
        }

        update_post_meta($post_id, '_course_schedule_structured', $clean_schedule);
    }

    // Сохраняем преподавателя
    $teacher_id = isset($_POST['course_teacher']) ? intval($_POST['course_teacher']) : 0;
    update_post_meta($post_id, '_course_teacher', $teacher_id);

    if (empty($clean_schedule) || empty($teacher_id)) {
        add_filter('redirect_post_location', function ($location) {
            return add_query_arg('course_meta_warning', 1, $location);
        });
    }
}
add_action('save_post', 'save_course_meta');

// Уведомление в админке
function course_admin_notice()
{
    if (isset($_GET['course_meta_warning'])) {
        echo '<div class="notice notice-warning is-dismissible"><p><strong>Внимание:</strong> Убедитесь, что вы добавили расписание и указали преподавателя.</p></div>';
    }
}
add_action('admin_notices', 'course_admin_notice');


add_action('wp_ajax_signup_for_lesson', 'signup_for_lesson');
add_action('wp_ajax_nopriv_signup_for_lesson', 'signup_for_lesson');
function signup_for_lesson()
{
    check_ajax_referer('my_ajax_nonce', 'nonce');

    $course_id = absint($_POST['course_id']);
    $day = absint($_POST['day']);
    $slot = absint($_POST['slot']);
    $user_id = get_current_user_id();

    if (!$user_id) {
        wp_send_json_error(['message' => 'Вы должны войти в систему.']);
    }

    $registrations = get_post_meta($course_id, '_course_registrations', true);
    if (!is_array($registrations)) {
        $registrations = [];
    }

    // Проверка, занят ли слот
    if (isset($registrations[$day][$slot]) && !empty($registrations[$day][$slot])) {
        wp_send_json_error(['message' => 'Это занятие уже занято.']);
    }

    $registrations[$day][$slot] = $user_id;

    update_post_meta($course_id, '_course_registrations', $registrations);

    wp_send_json_success();
}

add_action('wp_ajax_unsubscribe_from_lesson', 'ajax_signout_from_lesson');

function ajax_signout_from_lesson()
{
    if (!is_user_logged_in()) {
        wp_send_json_error('Требуется вход');
    }

    $user_id = get_current_user_id();
    $course_id = intval($_POST['course_id'] ?? 0);
    $day = intval($_POST['day'] ?? -1);
    $lesson_number = intval($_POST['lesson_number'] ?? -1);

    $signups = get_post_meta($course_id, '_course_signups', true) ?: [];

    if (isset($signups[$day][$lesson_number])) {
        $signups[$day][$lesson_number] = array_values(array_diff($signups[$day][$lesson_number], [$user_id]));
        update_post_meta($course_id, '_course_signups', $signups);
        wp_send_json_success();
    }

    wp_send_json_error('Невозможно отписаться');
}

function theme_enqueue_scripts() {
    wp_enqueue_script(
        'ajax-script',
        get_template_directory_uri() . '/js/ajax.js',
        array(), // зависимости, например ['jquery'] если нужно
        null,
        true // загрузить в footer
    );

    // Локализация для передачи admin-ajax.php URL в JS
    wp_localize_script('ajax-script', 'ajax_object', [
        'ajax_url' => admin_url('admin-ajax.php'),
    ]);
}
add_action('wp_enqueue_scripts', 'theme_enqueue_scripts');
