<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prikbord_comment_submit'])) {
    $post_id = intval($_POST['post_id']);
    $comment = wp_kses_post($_POST['reactie']);
    $email = sanitize_email($_POST['email']);
    $name = sanitize_text_field($_POST['naam']);

    $comments = get_post_meta($post_id, 'prikbord_reacties', true);
    if (!is_array($comments)) $comments = [];

    $comments[] = [
        'naam' => $name,
        'email' => $email,
        'reactie' => $comment,
        'datum' => current_time('mysql'),
    ];

    update_post_meta($post_id, 'prikbord_reacties', $comments);
    echo "<p>Reactie geplaatst!</p>";
}
?>

<form method="post">
    <input type="hidden" name="post_id" value="<?= get_the_ID() ?>">
    <input type="text" name="naam" placeholder="Naam" required>
    <input type="email" name="email" placeholder="E-mail" required>
    <?php wp_editor('', 'reactie', ['textarea_name' => 'reactie']); ?>

    <div class="g-recaptcha" data-sitekey="JOUW_SITE_KEY"></div>
    <script src='https://www.google.com/recaptcha/api.js'></script>

    <input type="submit" name="prikbord_comment_submit" value="Reageer">
</form>
