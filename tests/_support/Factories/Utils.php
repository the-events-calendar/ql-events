<?php

namespace QL_Events\Test\Factories;

/**
 * Slugify string
 *
 * @param string $text     Text input.
 * @param string $divider  slug spacer.
 *
 * @return string
 */
function slugify($text, string $divider = '-') {
  // Replace non letter or digits by divider.
  $text = preg_replace( '~[^\pL\d]+~u', $divider, $text );

  // Transliterate.
  $text = iconv( 'utf-8', 'us-ascii//TRANSLIT', $text );

  // Remove unwanted characters.
  $text = preg_replace( '~[^-\w]+~', '', $text );

  // Trim.
  $text = trim( $text, $divider );

  // Remove duplicate divider.
  $text = preg_replace( '~-+~', $divider, $text );

  // Lowercase.
  $text = strtolower( $text );

  if ( empty( $text ) ) {
    return 'n-a';
  }

  return $text;
}
