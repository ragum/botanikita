<?php
/**
 * The main template file
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

$context = Timber::context();
$context['post'] = Timber::get_post();
$context['testimonials'] = [
  [
    'name' => 'Dewi Nurhayati',
    'company' => [
      'id' => 'Pecinta Tanaman Hias',
      'en' => 'Tropical Plant Enthusiast',
    ],
    'text' => [
      'id' => 'Botanikita sangat membantu saya mengenal jenis-jenis tanaman hias tropis!',
      'en' => 'Botanikita really helped me get to know tropical houseplants!',
    ],
  ],
  [
    'name' => 'Ali Rahman',
    'company' => [
      'id' => 'Kolektor Bonsai',
      'en' => 'Bonsai Collector',
    ],
    'text' => [
      'id' => 'Tampilan portofolionya rapi dan informatif. Desainnya pun sangat alami.',
      'en' => 'The portfolio layout is neat and informative. The design feels very natural.',
    ],
  ],
  [
    'name' => 'Sari Amelia',
    'company' => [
      'id' => 'Pelanggan Setia',
      'en' => 'Loyal Customer',
    ],
    'text' => [
      'id' => 'Saya selalu menantikan tips baru dari Botanikita setiap minggunya!',
      'en' => 'I always look forward to new tips from Botanikita every week!',
    ],
  ],
];


$context['koleksi'] = Timber::get_posts([
  'post_type' => 'tanaman',
  'posts_per_page' => 3,
]);

$context['tips'] = Timber::get_posts([
  'post_type' => 'tips',
  'posts_per_page' => 3,
]);

Timber::render('front-page.twig', $context);
