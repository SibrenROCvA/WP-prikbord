<?php
/*
Plugin Name: Prikbord Plugin
Description: Frontend prikbord met goedkeuring en reacties
Version: 1.0
Author: Sibren
*/

defined('ABSPATH') or die();

require_once plugin_dir_path(__FILE__) . 'includes/post-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/comment-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin-approval.php';

add_action('init', 'prikbord_register_post_type');

function prikbord_register_post_type() {
    register_post_type('prikbord_post', [
        'label' => 'Prikbord',
        'public' => false,
        'show_ui' => true,
        'supports' => ['title', 'editor'],
        'capability_type' => 'post',
        'has_archive' => false,
    ]);
}

add_shortcode('prikbord_formulier', 'prikbord_formulier_shortcode');
add_shortcode('prikbord_overzicht', 'prikbord_overzicht_shortcode');
add_shortcode('prikbord_detail', 'prikbord_detail_shortcode');

function prikbord_formulier_shortcode() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'templates/form-post.php';
    return ob_get_clean();
}

function prikbord_overzicht_shortcode() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'templates/prikbord-display.php';
    return ob_get_clean();
}

add_action('init', 'prikbord_register_taxonomy');

function prikbord_register_taxonomy() {
    register_taxonomy('prikbord_categorie', 'prikbord_post', [
        'label'        => 'Categorieën',
        'public'       => true,
        'hierarchical' => true,
        'rewrite'      => ['slug' => 'prikbord-categorie'],
        'show_admin_column' => true,
        'show_in_rest' => true, // Belangrijk voor Gutenberg en REST API
    ]);
}


function prikbord_detail_shortcode($atts) {
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        return '<p>Geen geldig bericht geselecteerd.</p>';
    }

    $post_id = intval($_GET['id']);
    $post = get_post($post_id);

    if (!$post || $post->post_type !== 'prikbord_post' || $post->post_status !== 'publish') {
        return '<p>Bericht niet gevonden of niet gepubliceerd.</p>';
    }

    ob_start();
    ?>
    <div class="prikbord-detail">
        <h2><?php echo esc_html($post->post_title); ?></h2>
        <div><?php echo apply_filters('the_content', $post->post_content); ?></div>

        <h3>Reageren:</h3>
        <?php include plugin_dir_path(__FILE__) . 'templates/form-comment.php'; ?>

        <h3>Reacties:</h3>
        <?php
        $reacties = get_post_meta($post_id, 'prikbord_reacties', true);
        $eigenaar_email = get_post_meta($post_id, 'contact_email', true);
        $huidige_user = wp_get_current_user();

        if (is_array($reacties)) {
            foreach ($reacties as $r) {
                echo "<div class='reactie'>";
                echo "<p><strong>" . esc_html($r['naam']) . "</strong> schreef:</p>";
                echo "<p>" . wp_kses_post($r['reactie']) . "</p>";
                if ($huidige_user->user_email === $eigenaar_email) {
                    echo "<small>Contact: " . esc_html($r['email']) . "</small>";
                }
                echo "</div><hr>";
            }
        } else {
            echo "<p>Geen reacties.</p>";
        }
        ?>
    </div>
    <?php
    return ob_get_clean();
}
