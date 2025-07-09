<?php

namespace Botanikita\Routes;
use Timber\Timber;

class PageRoutes {
	public static function handle($template) {
		if (is_page('tanaman-hias')) {
        $context = Timber::context();
        $paged = get_query_var('paged') ?: 1;
        $filter_jenis = get_query_var('jenis');

        $args = [
            'post_type' => 'tanaman',
            'posts_per_page' => get_option('posts_per_page'),
            'paged' => $paged,
        ];

        if ($filter_jenis) {
            $args['tax_query'] = [[
                'taxonomy' => 'jenis_tanaman',
                'field' => 'slug',
                'terms' => $filter_jenis,
            ]];
        }
        error_log('paged = ' . get_query_var('paged'));
        $query = new \WP_Query($args);

        // ⛔ Tambahkan redirect jika page > max pages
        if ( $paged > 1 && ( $query->post_count === 0 || $paged > $query->max_num_pages ) ) {
            $redirect_url = get_permalink();
            if ( $filter_jenis ) {
                $redirect_url = add_query_arg('jenis', $filter_jenis, $redirect_url);
            }

            error_log('⛔ Redirect triggered because page ' . $paged . ' has no posts or exceeds max pages');
            wp_redirect( $redirect_url );
            exit;
        }


        $context['posts'] = new \Timber\PostQuery($query);
        Timber::render('page-tanaman-hias.twig', $context);
        return false;
    }

		return $template;
	}
}

// This class handles the custom routes for the "tanaman-hias" page.
// It checks if the current request is for the "tanaman-hias" page and renders
// the appropriate template with the posts context. If it is not, it returns the
// original template to continue the normal WordPress template loading process. 
// This allows for custom handling of specific pages while leaving other pages to be handled by WordPress and Timber automatically.
// This is useful for creating custom layouts or functionality for specific pages in your WordPress theme.
// You can add more methods to handle other custom routes as needed.
