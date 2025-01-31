# Sage Child Theme Support

[![Code Style](https://github.com/yardinternet/skeleton-package/actions/workflows/format-php.yml/badge.svg?no-cache)](https://github.com/yardinternet/skeleton-package/actions/workflows/format-php.yml)
[![PHPStan](https://github.com/yardinternet/skeleton-package/actions/workflows/phpstan.yml/badge.svg?no-cache)](https://github.com/yardinternet/skeleton-package/actions/workflows/phpstan.yml)
<!-- [![Tests](https://github.com/yardinternet/skeleton-package/actions/workflows/run-tests.yml/badge.svg?no-cache)](https://github.com/yardinternet/skeleton-package/actions/workflows/run-tests.yml) -->
<!-- [![Code Coverage Badge](https://github.com/yardinternet/skeleton-package/blob/badges/coverage.svg)](https://github.com/yardinternet/skeleton-package/actions/workflows/badges.yml) -->

Classes to use Acorn with child themes:

- WP like inheritance for config files; child config will override parent configuration.
- No directory scans, everything is configuration based.

## Requirements

- [Acorn](https://github.com/roots/acorn) >= 4.0

## Installation

To install this package using Composer, follow these steps:

1. Add the following to the `repositories` section of your `composer.json`:

    ```json
    {
      "type": "vcs",
      "url": "git@github.com:yardinternet/sage-child-theme-support.git"
    }
    ```

2. Install this package with Composer:

    ```sh
    composer require yard/sage-child-theme-support
    ```

## Configuration

1. Create a child theme with Sage as the parent theme ([How To Create A Child Theme | Wordpress.org](https://developer.wordpress.org/themes/advanced-topics/child-themes/#how-to-create-a-child-theme))

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

2. Add PSR-4 autoloading for your child theme to your (root) `composer.json`:

    ```diff
    "autoload": {
      "psr-4": {
        "App\\": "web/app/themes/sage/app/",
    +   "ChildTheme\\App\\": "web/app/themes/child-theme/app/",
      }
    },
    ```

    Remove the autoloading from your theme `composer.json` if applicable.

3. In `sage/config/app.php` change:

    ```diff
    -use Roots\Acorn\ServiceProvider;
    +use Yard\SageChildThemeSupport\ServiceProvider;
    ```

4. In `sage/functions.php` change:

    ```diff
    -\Roots\bootloader()->boot();
    +define('ACORN_BASEPATH', __DIR__);
    +\Yard\SageChildThemeSupport\bootloader()->boot();
    ```

5. Add view composers to `config/view.php`

    ```diff
    -  'composers' => [],
    +  'composers => [
    +    'app' => App\View\Composers\App::class,
    +    'comments' => App\View\Composers\Comments::class,
    +    'post' => App\View\Composers\Post::class,
    +  ],
    ```

6. Add any custom console commands to `config/console.php`:

    ```diff
    +  'commands => [
    +    'test' => App\Console\Commands\Test::class,
    +  ],
    ```

> [!IMPORTANT]
> After this change:
>
> - View Composers in the app/View/Composers directory will no longer be loaded automatically. To ensure they are registered, you have to configure them manually.
> - Console Commands in the app/Console/Commands directory will no longer be loaded automatically. To ensure they are register, you have to configure them manually.

## Usage

To override configuration for your child theme add the relevant files to the `child-theme/config` directory.
The configuration for the child theme is merged with the parent configuration where child theme configuration takes precedence. To unset a configuration option from the parent theme in the child theme you can pass an empty array for that configuration option.
