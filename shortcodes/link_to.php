<?php
/**
 * Adds a shortcode called 'link_to' that generates a link based on the provided attributes.
 *
 * @param array $atts The attributes passed to the shortcode.
 * @param string|null $content The content within the shortcode.
 * @return string The generated link HTML.
 */
add_shortcode('link_to', function ($atts, $content = null) {
    try {
        $atts = shortcode_atts(
            array(
                'slug' => '',
                'class' => '',
                'id' => '',
                'is_woo_cat' => false,
                'target' => '_self',
            ),
            $atts
        );

        $slug = sanitize_text_field($atts['slug']);
        $class = sanitize_text_field($atts['class']);
        $id = sanitize_text_field($atts['id']);
        $is_woo_cat = filter_var($atts['is_woo_cat'], FILTER_VALIDATE_BOOLEAN);
        $target = sanitize_text_field($atts['target']);

        $href = 'javascript:void(0);';
        $current = false;

        if (!empty($slug)) {
            if ($is_woo_cat) {
                $category = get_term_by('slug', $slug, 'product_cat');
                if ($category) {
                    $href = esc_url(get_term_link($category));
                    $current = get_queried_object_id() === $category->term_id;
                }
            } else {
                $page = get_page_by_path($slug, OBJECT, 'page');
                $post = get_page_by_path($slug, OBJECT, 'post');
                $category = get_category_by_slug($slug);

                if ($category) {
                    $href = esc_url(get_category_link($category->term_id));
                    $current = is_category($category->term_id);
                } elseif ($page) {
                    $href = esc_url(get_permalink($page));
                    $current = get_the_ID() === $page->ID;
                } elseif ($post) {
                    $href = esc_url(get_permalink($post));
                    $current = get_the_ID() === $post->ID;
                } elseif (post_type_exists($slug)) {
                    $href = esc_url(home_url($slug));
                    $current_url = trailingslashit(home_url(add_query_arg(array())));
                    $parts = parse_url($current_url);
                    $path = isset($parts['path']) ? trim($parts['path'], '/') : '';
                    $current = $path === $slug;
                }
            }
        }

        $class_attr = $class ? ' class="' . esc_attr($class) . ($current ? ' current' : '') . '"' : '';
        $id_attr = $id ? ' id="' . esc_attr($id) . '"' : '';
        $target_attr = $target ? ' target="' . esc_attr($target) . '"' : '';

        return '<a href="' . $href . '"' . $class_attr . $id_attr . $target_attr . '>' . do_shortcode($content) . '</a>';

    } catch (Exception $e) {
        return 'Error in link_to shortcode. Cause: ' . esc_html($e->getMessage());
    }
});
