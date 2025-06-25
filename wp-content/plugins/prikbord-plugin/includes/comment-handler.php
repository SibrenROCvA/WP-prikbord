<?php
// Verhinder directe toegang
defined('ABSPATH') or die('No direct access allowed');

add_action('init', 'prikbord_handle_comment_submission');

function prikbord_handle_comment_submission() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
    if (!isset($_POST['prikbord_comment_submit'])) return;

    // CAPTCHA controle (optioneel)
    // if (!isset($_POST['g-recaptcha-response'])) return;

    // Nonce check (optioneel, voor veiligheid)
    // if (!isset($_POST['prikbord_comment_nonce']) || !wp_verify_nonce($_POST['prikbord_comment_nonce'], 'prikbord_comment')) return;

    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $naam = sanitize_text_field($_POST['naam'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $reactie = wp_kses_post($_POST['reactie'] ?? '');

    if (!$post_id || empty($naam) || empty($email) || empty($reactie)) {
        return; // Fout: verplichte velden ontbreken
    }

    // Ophalen bestaande reacties
    $bestaande_reacties = get_post_meta($post_id, 'prikbord_reacties', true);
    if (!is_array($bestaande_reacties)) {
        $bestaande_reacties = [];
    }

    // Voeg nieuwe reactie toe
    $bestaande_reacties[] = [
        'naam' => $naam,
        'email' => $email,
        'reactie' => $reactie,
        'datum' => current_time('mysql')
    ];

    update_post_meta($post_id, 'prikbord_reacties', $bestaande_reacties);

    // Optioneel: redirect of melding
}
