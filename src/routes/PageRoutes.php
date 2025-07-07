<?php

namespace Botanikita\Routes;

use Timber\Timber;

class PageRoutes {
	public static function handle($template) {
		if (is_page('tanaman-hias')) {
			$context = Timber::context();
			$context['posts'] = Timber::get_posts([
				'post_type' => 'tanaman',
				'posts_per_page' => -1,
			]);
			Timber::render('page-tanaman-hias.twig', $context);
			return false;
		}

		// fallback
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
