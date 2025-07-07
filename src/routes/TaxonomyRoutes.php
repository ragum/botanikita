<?php

namespace Botanikita\Routes;

use Timber\Timber;

class TaxonomyRoutes {
	public static function handle($template) {
		if (is_tax('jenis_tanaman')) {
			$context = Timber::context();
			$context['term'] = Timber::get_term();
			Timber::render(['taxonomy-jenis-tanaman.twig', 'taxonomy.twig'], $context);
			return false;
		}

		return $template;
	}
}
// This class handles the custom routes for taxonomy 'jenis_tanaman'.
// It checks if the current request is for the 'jenis_tanaman' taxonomy and renders
// the appropriate template with the term context. If it is not, it returns the
// original template to continue the normal WordPress template loading process.
// This allows for custom handling of specific taxonomies while leaving other taxonomies to be handled by WordPress and Timber automatically.
// This is useful for creating custom layouts or functionality for specific taxonomies in your WordPress theme