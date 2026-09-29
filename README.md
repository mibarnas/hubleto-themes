# Themes for Hubleto

Four UI themes for Hubleto ERP, selectable in **Settings > Theme** next to the built-in ones:

| Theme | Look |
|---|---|
| `aurora` | Modern SaaS: Inter, violet-tinted neutrals, ink sidebar, gradient accents, pill shapes. |
| `folio` | Editorial and calm: Noto Serif titles over Noto Sans, warm paper neutrals, hairline borders. |
| `graphite` | Dense operations console: Noto Sans Mono figures, charcoal top bar, monochrome buttons, zebra rows. |
| `meridian` | Corporate: Lato, blue-grey neutrals, navy sidebar, flat bordered surfaces. |

All four support dark mode. Fonts are bundled, so the themes make no external requests.

## Requirements

- Hubleto ERP 1.1 (`composer create-project hubleto/erp-project`).
- PHP 8.2+.

## Installation

All commands run from the root of the Hubleto project.

1. **Copy the package** into the project, for example to `packages/hubleto-themes/`, so that this README is at `packages/hubleto-themes/README.md`.

2. **Make Composer copy the themes into place on every install and update.** In the project's `composer.json`, add to `"scripts"`:

   ```json
   "post-install-cmd": ["@php vendor/mibarnas/hubleto-themes/bin/install-themes.php"],
   "post-update-cmd": ["@php vendor/mibarnas/hubleto-themes/bin/install-themes.php"]
   ```

   If these entries already exist, add the command to their lists.

   Hubleto serves themes only from `vendor/hubleto/assets/css/themes/`. Composer rebuilds `vendor/`, so without these scripts the themes disappear after the next `composer update`.

3. **Install it with Composer:**

   ```bash
   composer config repositories.hubleto-themes path packages/hubleto-themes
   composer require "mibarnas/hubleto-themes:^1.0"
   ```

   The output ends with `Themes installed into .../vendor/hubleto/assets/css/themes`.

4. **Offer the themes in Settings > Theme.** In `boot.php`, add this after the `vendor/autoload.php` line and before `new \Hubleto\Erp\Loader($config)`:

   ```php
   \Hubleto\Framework\DependencyInjection::setServiceProvider(
       \Hubleto\App\Community\Settings\Controllers\Theme::class,
       \Mibarnas\HubletoThemes\Controllers\Theme::class
   );
   ```

   The built-in Theme page only accepts its own themes (default, grayscale, pink). This replaces it with a version that also lists every theme in this package.

After step 4, open **Settings > Theme** and pick a theme. No frontend rebuild is needed.

## Updating

Replace the package folder, then run:

```bash
composer update mibarnas/hubleto-themes
```

The post-update script copies the new files into place.

## Editing or adding a theme

Each theme is `themes/<name>.css`, with its fonts in `themes/<name>/fonts/`. The layout and component rules are shared in `themes/shared/base.css`; a theme file only imports it and sets the `--au-*` tokens (colours, fonts, radii, shadows) plus a few rules of its own. `themes/aurora.css` is the annotated reference for the full token set.

To add a theme, copy `aurora.css` to `themes/<name>.css` and change the tokens. The Theme page picks it up automatically.

A theme that leaves out a token doesn't cause an error. The browser silently falls back, so check with:

```bash
php bin/check-themes.php   # run from the package root
```

It lists any tokens missing from each theme and exits with 1 if there are any.

Then run `composer update mibarnas/hubleto-themes` in the project, or `php vendor/mibarnas/hubleto-themes/bin/install-themes.php` if Composer has nothing to update.

## Uninstalling

First switch back to a built-in theme in **Settings > Theme**. Otherwise Hubleto keeps linking a stylesheet that no longer exists. Then remove the `boot.php` lines and the `composer.json` script entries, and run `composer remove mibarnas/hubleto-themes`. The copied files are cleared the next time Composer reinstalls `hubleto/assets`.

## Licenses

The bundled fonts are under the SIL Open Font License 1.1; see `LICENSE-*.txt` in each `themes/<name>/fonts/` folder.
