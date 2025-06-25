<?php
// Verhinder directe toegang
defined('ABSPATH') or die('No direct access allowed');

add_filter('manage_prikbord_post_posts_columns', function($columns) {
    $columns['contact'] = 'Contact';
    return $columns;
});

add_action('manage_prikbord_post_posts_custom_column', function($column, $post_id) {
    if ($column === 'contact') {
        echo esc_html(get_post_meta($post_id, 'contact_email', true));
    }
}, 10, 2);
