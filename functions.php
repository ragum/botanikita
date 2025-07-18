<?php
/**
 * Timber starter-theme
 * https://github.com/timber/starter-theme
 */

// Load Composer dependencies.
/**
 * Theme Functions for Botanikita
 */

require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/src/StarterSite.php';

Timber\Timber::init();
Timber::$dirname = ['templates', 'views'];

// Register Custom Post Types and Taxonomies
add_action('init', function () {
    register_post_type('tanaman', [
        'labels' => [
            'name' => 'Tanaman',
            'singular_name' => 'Tanaman'
        ],
        'public' => true,
        'has_archive' => true,
        'rewrite' => ['slug' => 'tanaman'],
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'thumbnail']
    ]);

    register_taxonomy('jenis_tanaman', 'tanaman', [
        'labels' => [
            'name' => 'Jenis Tanaman',
            'singular_name' => 'Jenis Tanaman',
        ],
        'public' => true,
        'hierarchical' => true,
        'rewrite' => ['slug' => 'jenis-tanaman'],
        'show_in_rest' => true,
    ]);

    register_post_type('tips', [
        'labels' => [
            'name' => 'Tips',
            'singular_name' => 'Tips',
        ],
        'public' => true,
        'has_archive' => true,
        'rewrite' => ['slug' => 'tips-menanam'],
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'thumbnail'],
    ]);

    register_post_type('jurnal', [
        'labels' => [
            'name' => 'Jurnal Botani',
            'singular_name' => 'Jurnal',
        ],
        'public' => true,
        'has_archive' => true,
        'rewrite' => ['slug' => 'jurnal-botani'],
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'thumbnail'],
    ]);
});

// Rewrite and Query Vars
add_action('init', function () {
    add_rewrite_tag('%paged%', '([0-9]+)');
});

add_filter('query_vars', function ($vars) {
    $vars[] = 'jenis';
    return $vars;
});

// Register Menus
register_nav_menus([
    'primary_id' => __('Primary Menu (Bahasa Indonesia)', 'botanikita'),
    'primary_en' => __('Primary Menu (English)', 'botanikita'),
]);

// Menu Class Filters
add_filter('nav_menu_css_class', function($classes, $item, $args, $depth) {
    if ($args->theme_location === 'primary_id' || $args->theme_location === 'primary_en') {
        $classes[] = 'relative group';
    }
    return $classes;
}, 10, 4);

add_filter('nav_menu_link_attributes', function($atts, $item, $args, $depth) {
    if ($args->theme_location === 'primary_id' || $args->theme_location === 'primary_en') {
        $atts['class'] = 'block py-5 text-white text-sm font-medium hover:underline';
    }
    return $atts;
}, 10, 4);

// Twig Config
add_filter('timber/twig/environment/options', function ($options) {
    $options['autoescape'] = false;
    return $options;
});

add_filter('timber/context', function($context) {
    $context['term'] = Timber::get_term();
    $context['menu'] = get_polylang_menu();
    return $context;
});

// Helper: Convert WP_Post menu items to associative array
function convert_menu_items_to_array($items) {
    $result = [];
    foreach ($items as $item) {
        $data = [
            'ID' => $item->ID,
            'title' => $item->title,
            'url' => $item->url,
            'target' => $item->target,
            'menu_item_parent' => (int) $item->menu_item_parent,
            'children' => [],
        ];
        $result[$item->ID] = $data;
    }
    return $result;
}

function get_polylang_menu() {
    $lang = function_exists('pll_current_language') ? pll_current_language() : 'id';
    $locations = get_nav_menu_locations();
    $location = $lang === 'en' ? 'primary_en' : 'primary_id';

    if (!isset($locations[$location]) || !$locations[$location]) {
        return null;
    }

    $menu_id = $locations[$location];
    $raw_items = wp_get_nav_menu_items($menu_id);

    if (!$raw_items) {
        return null;
    }

    // Susun array keyed by ID
    $items = [];
    foreach ($raw_items as $item) {
        $items[$item->ID] = [
            'id' => $item->ID,
            'title' => $item->title,
            'url' => $item->url,
            'target' => $item->target,
            'current' => $item->current ?? false,
            'menu_item_parent' => (int) $item->menu_item_parent,
            'children' => [],
        ];
    }

    // Bangun struktur nested
    $tree = [];
    foreach ($items as $id => &$item) {
        if ($item['menu_item_parent'] && isset($items[$item['menu_item_parent']])) {
            $items[$item['menu_item_parent']]['children'][] = &$item;
        } else {
            $tree[] = &$item;
        }
    }

    return (object)[
        'items' => $tree,
    ];
}

new StarterSite();
