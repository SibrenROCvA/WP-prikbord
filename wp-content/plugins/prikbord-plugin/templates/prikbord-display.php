<?php
$args = ['post_type' => 'prikbord_post', 'post_status' => 'publish'];

// Voeg categorie filter toe aan query als deze is geselecteerd
if (!empty($_GET['categorie'])) {
    $args['tax_query'] = [
        [
            'taxonomy' => 'prikbord_categorie',
            'field'    => 'slug',
            'terms'    => sanitize_text_field($_GET['categorie']),
        ]
    ];
}

// Voeg zoekterm toe als deze is ingevoerd
if (!empty($_GET['s'])) {
    $args['s'] = sanitize_text_field($_GET['s']);
}

$query = new WP_Query($args);
?>

<form method="get" id="prikbord-filter">
    <select name="categorie" onchange="document.getElementById('prikbord-filter').submit();">
        <option value="">-- Kies categorie --</option>
        <?php
        $categories = get_terms('prikbord_categorie', ['hide_empty' => false]);
        foreach ($categories as $cat) {
            $selected = (isset($_GET['categorie']) && $_GET['categorie'] == $cat->slug) ? 'selected' : '';
            echo "<option value='{$cat->slug}' $selected>{$cat->name}</option>";
        }
        ?>
    </select>

    <input type="text" name="s" value="<?= esc_attr($_GET['s'] ?? '') ?>" placeholder="Zoeken">
    <button type="submit">Filter</button>
</form>

<?php

if ($query->have_posts()) : 
    while ($query->have_posts()) : $query->the_post();
        $post_id = get_the_ID();
        ?>
        <div class="prikbord-post">
            <h2><?php the_title(); ?></h2>
            <div class="post-meta">
                <?php
                // Toon de categorieën van dit bericht
                $categories = get_the_terms(get_the_ID(), 'prikbord_categorie');
                if ($categories && !is_wp_error($categories)) {
                    echo '<span class="prikbord-categorieen">Categorie: ';
                    $cat_names = [];
                    foreach ($categories as $category) {
                        $cat_names[] = esc_html($category->name);
                    }
                    echo implode(', ', $cat_names);
                    echo '</span>';
                }
                ?>
            </div>
            <a href="<?php echo esc_url(add_query_arg('id', get_the_ID(), home_url('/prikbord-detail/'))); ?>">Bekijk bericht</a>
        </div>
    <?php endwhile;
else:
    echo '<p>Er zijn geen berichten gevonden die aan je criteria voldoen.</p>';
endif;
wp_reset_postdata();
