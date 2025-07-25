<?php

use Timber\Site;
use Botanikita\Routes\PageRoutes;
use Botanikita\Routes\TaxonomyRoutes;

/**
 * Class StarterSite
 */
class StarterSite extends Site {
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'theme_supports' ) );
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );

		add_filter( 'timber/context', array( $this, 'add_to_context' ) );
		add_filter( 'timber/twig', array( $this, 'add_to_twig' ) );
		add_filter( 'timber/twig/environment/options', [ $this, 'update_twig_environment_options' ] );

        // Custom template for taxonomy
        add_filter('template_include', [$this, 'override_template']);
		add_filter('query_vars', function($vars) {
			$vars[] = 'jenis';
			$vars[] = 'paged'; // Ensure 'paged' is available for pagination
			return $vars;
		});

		parent::__construct();
	}

	/**
	 * This is where you can register custom post types.
	 */
	public function register_post_types() {

	}

	/**
	 * This is where you can register custom taxonomies.
	 */
	public function register_taxonomies() {
		add_rewrite_tag('%jenis%', '([^&]+)');
	}

	/**
	 * This is where you add some context
	 *
	 * @param string $context context['this'] Being the Twig's {{ this }}.
	 */
	public function add_to_context( $context ) {
		$context['foo']   = 'bar';
		$context['stuff'] = 'I am a value set in your functions.php file';
		$context['notes'] = 'These values are available everytime you call Timber::context();';
		$context['menu']  = Timber::get_menu();
		$context['site']  = $this;

		return $context;
	}

	public function theme_supports() {
		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

		/*
		 * Enable support for Post Formats.
		 *
		 * See: https://codex.wordpress.org/Post_Formats
		 */
		add_theme_support(
			'post-formats',
			array(
				'aside',
				'image',
				'video',
				'quote',
				'link',
				'gallery',
				'audio',
			)
		);

		add_theme_support( 'menus' );
	}

	/**
	 * his would return 'foo bar!'.
	 *
	 * @param string $text being 'foo', then returned 'foo bar!'.
	 */
	public function myfoo( $text ) {
		$text .= ' bar!';
		return $text;
	}

	/**
	 * This is where you can add your own functions to twig.
	 *
	 * @param Twig\Environment $twig get extension.
	 */
	public function add_to_twig( $twig ) {
		/**
		 * Required when you want to use Twig’s template_from_string.
		 * @link https://twig.symfony.com/doc/3.x/functions/template_from_string.html
		 */
		// $twig->addExtension( new Twig\Extension\StringLoaderExtension() );

		$twig->addFilter( new Twig\TwigFilter( 'myfoo', [ $this, 'myfoo' ] ) );

		return $twig;
	}

	/**
	 * Updates Twig environment options.
	 *
	 * @link https://twig.symfony.com/doc/2.x/api.html#environment-options
	 *
	 * \@param array $options An array of environment options.
	 *
	 * @return array
	 */
	function update_twig_environment_options( $options ) {
	    // $options['autoescape'] = true;

	    return $options;
	}

    // Override the template for taxonomy 'jenis_tanaman'
    /**
     * Override the template for taxonomy 'jenis_tanaman'.
     * @param string $template The current template.
     * @return string|bool The overridden template or false to stop further processing.
    */

    public function override_template($template) {
		$template = TaxonomyRoutes::handle($template);
		if ($template === false) return false;

		$template = PageRoutes::handle($template);
		if ($template === false) return false;

		if ( is_post_type_archive('tanaman') ) {
			$context = Timber::context();

			$paged = max(1, (int) get_query_var('paged'));
			$filter_jenis = get_query_var('jenis');

			// Query dasar
			$args = [
				'post_type' => 'tanaman',
				'posts_per_page' => get_option('posts_per_page'),
				'paged' => $paged,
			];

			// Tambah filter jika ada
			if ($filter_jenis) {
				$args['tax_query'] = [
					[
						'taxonomy' => 'jenis_tanaman',
						'field'    => 'slug',
						'terms'    => $filter_jenis,
					]
				];
			}

			$query = new \WP_Query($args);

			// Jika hasil kosong di page > 1, reset ke halaman 1 (tanpa redirect)
			if ($paged > 1 && $query->found_posts <= 0) {
				$new_url = get_post_type_archive_link('tanaman');
				if ($filter_jenis) {
					$new_url = add_query_arg('jenis', $filter_jenis, $new_url);
				}
				wp_redirect($new_url);
				exit;
			}

			$context['posts'] = new \Timber\PostQuery($query);

			Timber::render('archive-tanaman.twig', $context);
			return false;
		}


		// fallback ke default WP template loader
		return $template;
	}


}
