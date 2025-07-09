<?php
/**
 * Timber starter-theme
 * https://github.com/timber/starter-theme
 */

// Load Composer dependencies.
require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/src/StarterSite.php';

Timber\Timber::init();

// Sets the directories (inside your theme) to find .twig files.
Timber::$dirname = [ 'templates', 'views' ];

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
});

add_action('init', function () {
	register_taxonomy('jenis_tanaman', 'tanaman', [
		'labels' => [
			'name' => 'Jenis Tanaman',
			'singular_name' => 'Jenis Tanaman',
		],
		'public' => true,
		'hierarchical' => true, // mirip kategori, bukan tag
		'rewrite' => ['slug' => 'jenis-tanaman'],
		'show_in_rest' => true,
	]);
});

add_filter('timber/context', function ($context) {
    $context['term'] = Timber::get_term();
    return $context;
});

add_filter('timber/twig/environment/options', function ($options) {
    $options['autoescape'] = false;
    return $options;
});

add_filter('query_vars', function ($vars) {
    $vars[] = 'jenis';
    return $vars;
});

add_action('init', function () {
    add_rewrite_tag('%paged%', '([0-9]+)');
});

new StarterSite();
