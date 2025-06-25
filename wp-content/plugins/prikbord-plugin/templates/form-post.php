<?php
wp_enqueue_script('jquery');
wp_enqueue_script('wp-editor');
wp_enqueue_editor();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prikbord_post_submit'])) {
    // Validatie (nonce, captcha) etc.
    $post_id = wp_insert_post([
        'post_title' => sanitize_text_field($_POST['post_title']),
        'post_content' => wp_kses_post($_POST['post_content']),
        'post_type' => 'prikbord_post',
        'post_status' => 'pending'
    ]);

    if ($post_id) {
        // E-mail toevoegen
        add_post_meta($post_id, 'contact_email', sanitize_email($_POST['contact_email']));
        
        // Categorie instellen
        $categorie_id = isset($_POST['prikbord_categorie']) ? intval($_POST['prikbord_categorie']) : 0;
        if ($categorie_id > 0) {
            wp_set_object_terms($post_id, $categorie_id, 'prikbord_categorie');
        }
        
        echo "<p>Je bericht is ingediend en wacht op goedkeuring.</p>";
    }
}
?>

<form method="post">    <input type="text" name="post_title" placeholder="Titel" required>
    
    <div class="prikbord-categorie" style="margin: 10px 0;">
        <label for="prikbord_categorie">Categorie:</label>
        <select name="prikbord_categorie" id="prikbord_categorie" required>
            <option value="">-- Selecteer een categorie --</option>
            <?php 
            $categories = get_terms([
                'taxonomy' => 'prikbord_categorie',
                'hide_empty' => false,
            ]);
            
            if (!empty($categories) && !is_wp_error($categories)) {
                foreach ($categories as $category) {
                    echo '<option value="' . esc_attr($category->term_id) . '">' . esc_html($category->name) . '</option>';
                }
            }
            ?>
        </select>
    </div>
    
    <?php
    wp_editor('', 'post_content', ['textarea_name' => 'post_content']);
    ?>
    <input type="email" name="contact_email" placeholder="Je e-mailadres" required>

    <!-- Captcha (bijvoorbeeld hCaptcha of reCAPTCHA) -->
    <!-- <div class="g-recaptcha" data-sitekey="JOUW_SITE_KEY"></div>
    <script src='https://www.google.com/recaptcha/api.js'></script> -->

    <input type="submit" name="prikbord_post_submit" value="Verstuur">
</form>
