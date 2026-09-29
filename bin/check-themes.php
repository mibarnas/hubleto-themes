<?php

/**
 * Checks that every theme in themes/ defines each `--au-*` token that the
 * shared stylesheet (themes/shared/base.css) uses.
 *
 * An undefined custom property does not raise an error in the browser -- the
 * declaration just silently falls back to its initial value -- so a theme that
 * misses a token breaks quietly. Run after adding or editing a theme:
 *
 *   php bin/check-themes.php   (from the package root)
 *
 * Exits with 1 and lists the missing tokens per theme if any are found.
 */

$themesDir = __DIR__ . '/../themes';

preg_match_all('/var\((--au-[a-z0-9-]+)/', file_get_contents($themesDir . '/shared/base.css'), $m);
$used = array_unique($m[1]);
sort($used);

$failed = false;
foreach (glob($themesDir . '/*.css') as $themeFile) {
  preg_match_all('/(--au-[a-z0-9-]+)\s*:/', file_get_contents($themeFile), $m);
  $missing = array_diff($used, array_unique($m[1]));

  $theme = pathinfo($themeFile, PATHINFO_FILENAME);
  if (count($missing) > 0) {
    $failed = true;
    echo "{$theme}: missing " . implode(', ', $missing) . "\n";
  } else {
    echo "{$theme}: ok (" . count($used) . " tokens)\n";
  }
}

exit($failed ? 1 : 0);
