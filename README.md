# CINQ Classic Editor

WordPress plugin that turns the block editor off for every post type and for widgets, and stops loading block styles on the front end. No settings screen.

The [Classic Editor](https://wordpress.org/plugins/classic-editor/) plugin brings back the classic editor, with a setting that lets each user switch back to the block editor. It leaves the front end as it is: WordPress still prints the block library, the global styles and the classic theme styles on every page. A theme that renders its own templates (ACF fields, Twig) uses none of them. This plugin replaces Classic Editor and removes those styles.

**Repository:** [`agencecinq/cinq-wp-classic-editor`](https://github.com/agencecinq/cinq-wp-classic-editor)

## Requirements

- WordPress 6.0+
- PHP 8.1+

## Lint (WordPress Coding Standards)

```bash
composer install
composer lint          # phpcs
composer lint:fix     # phpcbf (auto-fix)
```

Use `./vendor/bin/phpcs`, not the global `phpcs` binary — the global install does not register the WordPress standards.

## Install

Copy the plugin file into `mu-plugins`. WordPress loads it on every request, and it cannot be deactivated from the admin. It is listed under **Plugins → Must-Use**, not with the regular plugins.

```bash
cp cinq-wp-classic-editor.php /path/to/wp-content/mu-plugins/
```

Replace that file when the plugin changes. Deactivate and delete the Classic Editor plugin: this one does what it did.

To install it as a regular plugin, copy the folder to `wp-content/plugins/cinq-wp-classic-editor` and activate **CINQ Classic Editor** in the WordPress admin.

## Behaviour

In the admin:

- every post type is edited with the classic editor (`use_block_editor_for_post_type`);
- widgets are managed with the classic screen (`use_widgets_block_editor`).

On the front end, none of these styles is printed any more:

| Handle | Content |
| --- | --- |
| `wp-block-library` | Core block styles, bundled in one stylesheet |
| `wp-block-paragraph`, `wp-block-image`, … | The same styles, one per block, loaded when the block is rendered |
| `wp-block-library-theme` | Opinionated block styles, for themes that support `wp-block-styles` |
| `global-styles` | Presets and styles generated from `theme.json` |
| `classic-theme-styles` | Button and file block styles for classic themes |

Since WordPress 6.9, classic themes load each block's stylesheet when the block is rendered. The plugin turns that off (`should_load_separate_core_block_assets`), so the core block styles are bundled in `wp-block-library` again, then dequeues it. The `enqueue_block_assets` action still fires, for the plugins that use it.

## Keep one part

Every hook is a core callback or a named function, so the theme can undo one of them:

```php
// Keep the global styles.
add_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
add_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );

// Keep the block library stylesheet.
remove_action( 'wp_enqueue_scripts', 'cinq_wp_classic_editor_dequeue_block_styles', 11 );

// Use the block editor for one post type.
add_filter( 'use_block_editor_for_post_type', fn ( $use, $post_type ) => 'guide' === $post_type || $use, 11, 2 );
```

## Scope

- No options, no admin UI, no front-end assets
- Emoji styles and scripts are not touched

## License

MIT © [CINQ](https://agencecinq.com)
