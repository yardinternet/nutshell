# Yard Brave Child

[![Code Style](https://github.com/yardinternet/skeleton-package/actions/workflows/format-php.yml/badge.svg?no-cache)](https://github.com/yardinternet/skeleton-package/actions/workflows/format-php.yml)
[![PHPStan](https://github.com/yardinternet/skeleton-package/actions/workflows/phpstan.yml/badge.svg?no-cache)](https://github.com/yardinternet/skeleton-package/actions/workflows/phpstan.yml)
<!-- [![Tests](https://github.com/yardinternet/skeleton-package/actions/workflows/run-tests.yml/badge.svg?no-cache)](https://github.com/yardinternet/skeleton-package/actions/workflows/run-tests.yml) -->
<!-- [![Code Coverage Badge](https://github.com/yardinternet/skeleton-package/blob/badges/coverage.svg)](https://github.com/yardinternet/skeleton-package/actions/workflows/badges.yml) -->

Classes to use Acorn with child themes:

- WP like inheritance for config files; child config will override parent config
- No directory scans, everything is config based

## Requirements

- [Acorn](https://github.com/roots/acorn) >= 4.0

## Installation

To install this package using Composer, follow these steps:

1. Add the following to the `repositories` section of your `composer.json`:

    ```json
    {
      "type": "vcs",
      "url": "git@github.com:yardinternet/brave-child.git"
    }
    ```

2. Install this package with Composer:

    ```sh
    composer require yard/brave-child
    ```

## Configuration

1. Create a child theme with sage as the parent theme
  [How To Create A Child Theme | Wordpress.org](https://developer.wordpress.org/themes/advanced-topics/child-themes/#how-to-create-a-child-theme) Example `style.css`:

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

2. Add PSR-4 autoloading for your child theme to your composer.json:

    ```diff
    "autoload": {
      "psr-4": {
        "App\\": "web/app/themes/sage/app/",
    +   "Child\\App\\": "web/app/themes/child-theme/app/",
      }
    },
    ```

3. Add the following line to your config:

    ```php
    Config::define('ACORN_BASEPATH', Config::get('WP_CONTENT_DIR') . '/themes/sage');
    ```

4. In `sage/config/app.php` change:

    ```diff
    -use Roots\Acorn\ServiceProvider;
    +use Yard\BraveChild\ServiceProvider;
    ```

5. In `sage/functions.php` change:

     ```diff
     -\Roots\Bootloader()->boot();
     +$bootloader = \Roots\bootloader();
     +$bootloader->getApplication()->bind(
     +  \Roots\Acorn\Bootstrap\LoadConfiguration::class,
     +  \Yard\BraveChild\Bootstrap\LoadConfiguration::class
     +);
     +$bootloader->boot();
     ```

6. Add view composers to `config/view.php`

    > [!IMPORTANT]
    > After this change, View Composers in the app/View/Composers directory will no longer be loaded automatically. To ensure they are registered, you have to configure them manually.

    ```diff
    -  'composers' => [],
    +  'composers => [
    +    'app' => App\View\Composers\App::class,
    +    'comments' => App\View\Composers\Comments::class,
    +    'post' => App\View\Composers\Post::class,
    +  ],
    ```
