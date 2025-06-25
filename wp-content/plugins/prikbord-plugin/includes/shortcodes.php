add_shortcode('prikbord_formulier', 'prikbord_formulier_shortcode');

function prikbord_formulier_shortcode() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'templates/form-post.php';
    return ob_get_clean();
}

add_shortcode('prikbord_overzicht', 'prikbord_overzicht_shortcode');

function prikbord_overzicht_shortcode() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'templates/prikbord-display.php';
    return ob_get_clean();
}
