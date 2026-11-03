# Nutshell: Enhanced Acorn Support for WordPress Themes

[![Code Style](https://github.com/yardinternet/nutshell/actions/workflows/format-php.yml/badge.svg)](https://github.com/yardinternet/nutshell/actions/workflows/format-php.yml)
[![PHPStan](https://github.com/yardinternet/nutshell/actions/workflows/phpstan.yml/badge.svg?no-cache)](https://github.com/yardinternet/nutshell/actions/workflows/phpstan.yml)
![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/yard/nutshell/php)
![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/yard/nutshell/roots%2Facorn)

**Nutshell** is a feature-rich package designed to extend [Acorn](https://roots.io/acorn/) for WordPress themes. It provides a flexible foundation for advanced theme development, including configuration inheritance, Sentry integration, CSP header support, and more.

## Features

- **Child Theme Configuration Inheritance**:
  - Allows child themes to override parent configuration files without directory scans.
  - Uses a custom configuration repository to support unsetting and merging config values.
- **Sentry Integration**:
  - Seamless error reporting via [`sentry/sentry-laravel`](https://github.com/getsentry/sentry-laravel)
- **CSP Integration**:
  - Add CSP headers via [`spatie/laravel-csp`](https://github.com/spatie/laravel-csp)
- **Custom View Composers**:
  - Manual registration of view composers for fine-grained control.
- **Custom Console Commands**:
  - Register custom Artisan commands via configuration.

## Installation

1. Install this package with composer

   ```sh
   composer require yard/nutshell
   ```

2. Ensure your project's `composer.json` uses PSR-4 autoloading for your theme and childtheme and remove any redundant autoloading from the theme itself.

   ```diff
   "autoload": {
      "psr-4": {
      "App\\": "web/app/themes/sage/app/",
   +   "ChildTheme\\App\\": "web/app/themes/child-theme/app/",
      }
   },
   ```

## Configuration

> [!IMPORTANT]
> After this change:
>
> - View Composers in the app/View/Composers directory will no longer be loaded automatically. To ensure they are registered, you have to configure them manually.
> - Console Commands in the app/Console/Commands directory will no longer be loaded automatically. To ensure they are register, you have to configure them manually.

1. **Child Theme Setup**
   - Create a child theme with Sage as the parent theme. See [WordPress Child Themes](https://developer.wordpress.org/themes/advanced-topics/child-themes/#how-to-create-a-child-theme).

      Example `style.css`:

      ```css
      /**
         * Theme Name:         Sage Child Theme
         * Template:           sage
         * Theme URI:          https://www.example.com/sage-child/
         * Description:        Sage child theme
         * Version:            1.0.0
         * Author:             Example Inc.
         * Author URI:         http://www.example.com/
         * Text Domain:        sage
         * License:            MIT License
         * License URI:        https://opensource.org/licenses/MIT
         * Requires PHP:       8.1
         * Requires at least:  5.9
         */
         ```

   - Place your configuration files in `config/` within your child theme directory. These will be merged with the parent configuration where child theme configuration takes precedence. See [Configuration inheritance](#configuration-inheritance) for how merging works.

2. **Update Acorn Bootloader**

   - In your theme's `functions.php`, use `Yard\Nutshell\Application` to build Acorn with Nutshell's enhancements.

      ```diff
     +define('ACORN_BASEPATH', __DIR__);
     +
      add_action('after_setup_theme', function () {
     -    Application::configure()
     +    Yard\Nutshell\Application::configure()
            ->withProviders([
                  App\Providers\ThemeServiceProvider::class,
            ])
            ->boot();
      }, 0);
      ```

   - Define `ACORN_BASEPATH` in the **parent** theme. Without it Acorn infers the base path from the active stylesheet, so a child theme that has its own `app/` directory becomes the base path and the parent's `config/` is never loaded.

3. **Update app config**

   - In your themes `config/app.php' replace Acorn's ServiceProvider with Nutshell's ServiceProvider

      ```diff
      -use Roots\Acorn\ServiceProvider;
      +use Yard\Nutshell\ServiceProvider;
      ```

4. **Register View Composers**
   - Add your view composers to `config/view.php` under the `composers` key. Automatic discovery is disabled for explicit control.

      ```diff
      -  'composers' => [],
      +  'composers => [
      +    'app' => App\View\Composers\App::class,
      +    'comments' => App\View\Composers\Comments::class,
      +    'post' => App\View\Composers\Post::class,
      +  ],
      ```

5. **Register Console Commands**
   - Add custom Artisan commands to `config/console.php` under the `commands` key.

      ```diff
      +  'commands => [
      +    'test' => App\Console\Commands\Test::class,
      +  ],
      ```

6. **Sentry Integration**
   - Sentry is automatically integrated via `sentry/sentry-laravel`.

## Usage

- **Configuration Inheritance**: Any config file in your child theme's `config/` directory is merged into the parent's. A config file that returns an empty array unsets the corresponding configuration. See [Configuration inheritance](#configuration-inheritance).
- **View Composers**: Register all composers manually in `config/view.php`.
- **Console Commands**: Register all commands manually in `config/console.php`.
- **Sentry**: Errors and exceptions are reported to Sentry automatically.

## Configuration inheritance

When a child theme is active and has a `config/` directory, Nutshell first loads the parent theme's configuration as usual and then applies each file in the child theme's `config/` directory on top of it. Child themes without a `config/` directory simply use the parent configuration.

For every file in the child theme's `config/` directory:

| Child file                        | Result                                                                        |
| --------------------------------- | ----------------------------------------------------------------------------- |
| Does not exist                    | The parent's config file is used unchanged.                                   |
| Exists, parent has no such file   | The child's config file is added as-is.                                       |
| Exists and returns values         | Top-level keys are merged into the parent's config; the child's values win.   |
| Exists and returns an empty array | The whole config file is removed, as if the parent never defined it.          |

### The merge is shallow

Only the **top-level keys** of a config file are merged (using `array_merge`). A nested array in the child theme replaces the parent's nested array entirely; it is not merged recursively.

Given the parent theme's `config/view.php`:

```php
// web/app/themes/sage/config/view.php
return [
    'paths' => [
        resource_path('views'),
    ],
    'compiled' => storage_path('framework/views'),
    'composers' => [
        'app' => App\View\Composers\App::class,
        'post' => App\View\Composers\Post::class,
    ],
];
```

And the child theme's `config/view.php`:

```php
// web/app/themes/child-theme/config/view.php
return [
    'composers' => [
        'app' => ChildTheme\App\View\Composers\App::class,
    ],
];
```

The resulting `view` config is:

```php
[
    'paths' => [
        resource_path('views'),                              // from parent
    ],
    'compiled' => storage_path('framework/views'),           // from parent
    'composers' => [
        'app' => ChildTheme\App\View\Composers\App::class,   // from child, the 'post' composer is gone
    ],
]
```

`paths` and `compiled` are inherited because the child does not define them. `composers` is replaced as a whole, so the parent's `post` composer is no longer registered.

### Overriding a single value

To change one value, only define that key in the child theme. Everything else is inherited.

```php
// web/app/themes/child-theme/config/app.php
return [
    'name' => 'Child Theme',
];
```

### Extending a nested array of the parent

Because nested arrays are replaced, a child theme cannot add a single entry to a nested array of the parent by defining that array in the same file. Instead, move the nested array into its own config file in a subdirectory. Config files in subdirectories are loaded as nested keys, so `config/view/composers.php` becomes `view.composers`. The merge then happens on the entries of that file, which lets the child theme add or override individual entries.

Parent theme:

```php
// web/app/themes/sage/config/view/composers.php
return [
    'app' => App\View\Composers\App::class,
    'post' => App\View\Composers\Post::class,
];
```

Child theme:

```php
// web/app/themes/child-theme/config/view/composers.php
return [
    'app' => ChildTheme\App\View\Composers\App::class,       // overrides the parent's 'app' composer
    'footer' => ChildTheme\App\View\Composers\Footer::class, // adds a new composer
];
```

The resulting `view.composers` config is:

```php
[
    'app' => ChildTheme\App\View\Composers\App::class,       // from child
    'post' => App\View\Composers\Post::class,                // from parent
    'footer' => ChildTheme\App\View\Composers\Footer::class, // from child
]
```

The same approach works for console commands with `config/console/commands.php`.

> [!IMPORTANT]
> When a nested array lives in its own file, do not also define it in the parent file (e.g. a `composers` key in `config/view.php`). The subdirectory file is loaded after the parent file and replaces that key.

### Removing values from the parent

To clear a single top-level key, set it to an empty array (or another empty value). This replaces the parent's value:

```php
// web/app/themes/child-theme/config/view.php
return [
    'composers' => [], // no view composers are registered, all other view config is inherited
];
```

To remove a complete config file from the parent, let the child's config file return an empty array. This also works for files in subdirectories:

```php
// web/app/themes/child-theme/config/csp.php
return []; // config('csp') is now null
```

```php
// web/app/themes/child-theme/config/view/composers.php
return []; // config('view.composers') is now null, all other view config is inherited
```

> [!NOTE]
> The file has to `return []`. A file without a `return` statement is not treated as empty and will cause an error.

### Adding new config files

Config files that only exist in the child theme are loaded as-is and are available through `config()` like any other config file:

```php
// web/app/themes/child-theme/config/child.php
return [
    'feature_enabled' => env('CHILD_FEATURE_ENABLED', false),
];
```

```php
config('child.feature_enabled');
```

## About us

[![banner](https://raw.githubusercontent.com/yardinternet/.github/refs/heads/main/profile/assets/small-banner-github.svg)](https://www.yard.nl/werken-bij/)
