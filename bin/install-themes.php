<?php

/**
 * Copies this package's UI themes (themes/) into the Hubleto assets package.
 *
 * Desktop.twig links the active theme as `{assetsUrl}/css/themes/{uiTheme}.css`
 * and assetsUrl points into vendor/hubleto/assets, so a theme has to live there
 * to be served. vendor/ is rebuilt by Composer, which is why the host project
 * runs this script after every install/update (see README.md).
 *
 * Run from the root of the Hubleto project:
 *
 *   php vendor/mibarnas/hubleto-themes/bin/install-themes.php
 */

$autoload = getcwd() . '/vendor/autoload.php';
if (!is_file($autoload)) {
  fwrite(STDERR, "vendor/autoload.php not found, run this from the root of the Hubleto project.\n");
  exit(1);
}
require $autoload;

$assets = \Composer\InstalledVersions::isInstalled('hubleto/assets')
  ? \Composer\InstalledVersions::getInstallPath('hubleto/assets')
  : null;
$target = $assets === null ? null : realpath($assets . '/css/themes');

if (!$target) {
  fwrite(STDERR, "Hubleto assets (hubleto/assets) not found, run `composer install` first.\n");
  exit(1);
}

$source = __DIR__ . '/../themes';

$items = new RecursiveIteratorIterator(
  new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS),
  RecursiveIteratorIterator::SELF_FIRST
);

foreach ($items as $item) {
  $destination = $target . '/' . $items->getSubPathname();
  if ($item->isDir()) {
    if (!is_dir($destination)) mkdir($destination, 0775, true);
  } else {
    copy($item->getPathname(), $destination);
  }
}

echo "Themes installed into {$target}\n";
