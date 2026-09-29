<?php

// Registered in the host project's boot.php as the provider for
// \Hubleto\App\Community\Settings\Controllers\Theme (see README.md).

namespace Mibarnas\HubletoThemes\Controllers;

/**
 * Settings > Theme, extended with the themes from this package.
 *
 * The stock controller only accepts its hardcoded list (default, grayscale,
 * pink). Every `themes/<name>.css` of this package is offered here as well; the
 * files are served from the assets package, where bin/install-themes.php
 * copies them.
 */
class Theme extends \Hubleto\App\Community\Settings\Controllers\Theme
{
  public function prepareView(): void
  {
    $packageThemes = $this->getPackageThemes();

    // The parent redirects away (and exits) for its own themes, so handle ours first.
    $set = $this->router()->urlParamAsString('set');
    if (in_array($set, $packageThemes)) {
      $this->config()->save('uiTheme', $set);
      $this->router()->redirectTo($this->router()->getRoute());
    }

    parent::prepareView();

    $this->viewParams['themes'] = array_values(array_unique(array_merge(
      $this->viewParams['themes'] ?? [],
      $packageThemes
    )));
  }

  /**
   * @return array<int, string>
   */
  public function getPackageThemes(): array
  {
    $themes = [];
    foreach (glob(__DIR__ . '/../../themes/*.css') ?: [] as $file) {
      $themes[] = pathinfo($file, PATHINFO_FILENAME);
    }
    return $themes;
  }
}
