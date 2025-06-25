<?php
// Zorg dat dit bestand niet direct toegankelijk is
defined('ABSPATH') or die('No direct access allowed');

add_action('init', 'prikbord_handle_post_submission');

function prikbord_handle_post_submission() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
    if (!isset($_POST['prikbord_post_submit'])) return;

    // (Optioneel) check CAPTCHA
    // if (!isset($_POST['g-recaptcha-response'])) { return; }

    // Nonce check (voeg toe aan formulier voor meer veiligheid)
    // if (!isset($_POST['prikbord_nonce']) || !wp_verify_nonce($_POST['prikbord_nonce'], 'prikbord_post')) return;    $title = sanitize_text_field($_POST['post_title'] ?? '');
    $content = wp_kses_post($_POST['post_content'] ?? '');
    $contact_email = sanitize_email($_POST['contact_email'] ?? '');
    $categorie_id = isset($_POST['prikbord_categorie']) ? intval($_POST['prikbord_categorie']) : 0;

    if (empty($title) || empty($content) || empty($contact_email) || empty($categorie_id)) {
        return; // Fout: vereist veld leeg
    }

    // Maak nieuw bericht aan als 'pending'
    $post_id = wp_insert_post([
        'post_title' => $title,
        'post_content' => $content,
        'post_type' => 'prikbord_post',
        'post_status' => 'pending',
    ]);    if (!is_wp_error($post_id)) {
        // Contact e-mail opslaan
        add_post_meta($post_id, 'contact_email', $contact_email);
        
        // Categorie toewijzen aan het bericht
        if ($categorie_id > 0) {
            wp_set_object_terms($post_id, $categorie_id, 'prikbord_categorie');
        }
        
        // Eventueel: redirect of succesmelding
    }
}
