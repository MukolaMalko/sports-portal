<?php
/**
 * Sports Theme Functions
 * Реєструє CPT, ACF-поля, підключає REST API та JWT
 */

// ─── 1. Custom Post Types ────────────────────────────────────────────────────

function sports_register_cpt() {
    $types = [
        'event'    => ['label' => 'Події',       'menu_icon' => 'dashicons-calendar'],
        'schedule' => ['label' => 'Розклад',     'menu_icon' => 'dashicons-clock'],
        'results'  => ['label' => 'Результати',  'menu_icon' => 'dashicons-awards'],
        'news'     => ['label' => 'Новини',      'menu_icon' => 'dashicons-megaphone'],
    ];

    foreach ($types as $slug => $args) {
        register_post_type($slug, [
            'label'               => $args['label'],
            'public'              => true,
            'publicly_queryable'  => true,
            'show_in_rest'        => true,
            'rest_base'           => $slug,
            'supports'            => ['title','editor','thumbnail','custom-fields'],
            'menu_icon'           => $args['menu_icon'],
            'has_archive'         => true,
            'rewrite'             => ['slug' => $slug],
            'capability_type'     => 'post',
            'map_meta_cap'        => true,
        ]);
    }
}
add_action('init', 'sports_register_cpt');

// ─── 2. ACF Field Groups ─────────────────────────────────────────────────────

function sports_register_acf_fields() {
    if (!function_exists('acf_add_local_field_group')) return;

    acf_add_local_field_group([
        'key'    => 'group_events',
        'title'  => 'Поля події',
        'fields' => [
            ['key'=>'field_event_date',     'label'=>'Дата події',    'name'=>'event_date',      'type'=>'date_picker'],
            ['key'=>'field_event_location', 'label'=>'Місце',         'name'=>'event_location',  'type'=>'text'],
            ['key'=>'field_event_sport',    'label'=>'Вид спорту',    'name'=>'sport_type',      'type'=>'text'],
            ['key'=>'field_event_status',   'label'=>'Статус',        'name'=>'event_status',    'type'=>'select',
             'choices'=>['active'=>'Активна','finished'=>'Завершена','cancelled'=>'Скасована']],
            ['key'=>'field_gdrive_folder',  'label'=>'Google Drive Folder ID', 'name'=>'gdrive_folder_id','type'=>'text'],
        ],
        'location'=>[[['param'=>'post_type','operator'=>'==','value'=>'event']]],
    ]);

    acf_add_local_field_group([
        'key'    => 'group_schedule',
        'title'  => 'Поля розкладу',
        'fields' => [
            ['key'=>'field_sch_event',     'label'=>'Подія',          'name'=>'related_event', 'type'=>'post_object','post_type'=>['event']],
            ['key'=>'field_sch_stage',     'label'=>'Етап',           'name'=>'stage',         'type'=>'text'],
            ['key'=>'field_sch_start',     'label'=>'Початок',        'name'=>'start_time',    'type'=>'date_time_picker'],
            ['key'=>'field_sch_end',       'label'=>'Кінець',         'name'=>'end_time',      'type'=>'date_time_picker'],
            ['key'=>'field_sch_gsheet_id', 'label'=>'Google Sheet ID','name'=>'gsheet_id',     'type'=>'text'],
        ],
        'location'=>[[['param'=>'post_type','operator'=>'==','value'=>'schedule']]],
    ]);

    acf_add_local_field_group([
        'key'    => 'group_results',
        'title'  => 'Поля результатів',
        'fields' => [
            ['key'=>'field_res_event',       'label'=>'Подія',       'name'=>'related_event',  'type'=>'post_object','post_type'=>['event']],
            ['key'=>'field_res_participant', 'label'=>'Учасник',     'name'=>'participant_id', 'type'=>'text'],
            ['key'=>'field_res_score',       'label'=>'Рахунок',     'name'=>'score',          'type'=>'text'],
            ['key'=>'field_res_place',       'label'=>'Місце',       'name'=>'place',          'type'=>'number'],
            ['key'=>'field_res_gsheet_range','label'=>'Sheet Range', 'name'=>'gsheet_range',   'type'=>'text'],
        ],
        'location'=>[[['param'=>'post_type','operator'=>'==','value'=>'results']]],
    ]);

    acf_add_local_field_group([
        'key'    => 'group_news',
        'title'  => 'Поля новини',
        'fields' => [
            ['key'=>'field_news_pub_date', 'label'=>'Дата публікації',         'name'=>'publication_date', 'type'=>'date_picker'],
            ['key'=>'field_news_media',    'label'=>'Google Drive Media Folder','name'=>'media_folder_id', 'type'=>'text'],
        ],
        'location'=>[[['param'=>'post_type','operator'=>'==','value'=>'news']]],
    ]);
}
add_action('acf/init', 'sports_register_acf_fields');

// ─── 3. REST API — додати ACF поля ───────────────────────────────────────────

function sports_expose_acf_in_rest() {
    foreach (['event','schedule','results','news'] as $pt) {
        add_filter("rest_prepare_{$pt}", function($response, $post) {
            $acf = get_fields($post->ID);
            if ($acf) $response->data['acf'] = $acf;
            return $response;
        }, 10, 2);
    }
}
add_action('rest_api_init', 'sports_expose_acf_in_rest');

// ─── 4. Виправлення 403 — публічний доступ до GET запитів ────────────────────

add_filter('rest_pre_dispatch', function($result, $server, $request) {
    if ($request->get_method() === 'GET' && preg_match('#^/wp/v2/(event|news|schedule|results)#', $request->get_route())) {
        add_filter('rest_authentication_errors', '__return_null', 999);
    }
    return $result;
}, 10, 3);

// ─── 5. CORS Headers ─────────────────────────────────────────────────────────

add_action('rest_api_init', function() {
    remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');
    add_filter('rest_pre_serve_request', function($value) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: Authorization, Content-Type, X-WP-Nonce');
        return $value;
    });
}, 15);

// ─── 6. Стилі та скрипти ─────────────────────────────────────────────────────

function sports_enqueue() {
    wp_enqueue_style('sports-style', get_stylesheet_uri(), [], '1.0.1');
    wp_enqueue_script('sports-app', get_template_directory_uri().'/js/app.js', [], '1.0.1', true);
    wp_localize_script('sports-app', 'SportsAPI', [
        'root'    => esc_url_raw(rest_url()),
        'nonce'   => wp_create_nonce('wp_rest'),
        'siteUrl' => get_site_url(),
    ]);
}
add_action('wp_enqueue_scripts', 'sports_enqueue');

// ─── 7. Підтримка теми ───────────────────────────────────────────────────────

function sports_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form','comment-form','comment-list','gallery','caption']);
    register_nav_menus(['primary' => 'Головне меню', 'footer' => 'Підвал меню']);
}
add_action('after_setup_theme', 'sports_theme_setup');