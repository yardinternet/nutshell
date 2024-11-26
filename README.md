# Yard Brave Child

[![Code Style](https://github.com/yardinternet/skeleton-package/actions/workflows/format-php.yml/badge.svg?no-cache)](https://github.com/yardinternet/skeleton-package/actions/workflows/format-php.yml)
[![PHPStan](https://github.com/yardinternet/skeleton-package/actions/workflows/phpstan.yml/badge.svg?no-cache)](https://github.com/yardinternet/skeleton-package/actions/workflows/phpstan.yml)
<!-- [![Tests](https://github.com/yardinternet/skeleton-package/actions/workflows/run-tests.yml/badge.svg?no-cache)](https://github.com/yardinternet/skeleton-package/actions/workflows/run-tests.yml) -->
<!-- [![Code Coverage Badge](https://github.com/yardinternet/skeleton-package/blob/badges/coverage.svg)](https://github.com/yardinternet/skeleton-package/actions/workflows/badges.yml) -->

Classes to use Acorn with child themes:

- WP like inheritance for config files; child config will override parent config
- No directory scans, everything is config base

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

Add the following line to your config:

```php
Config::define('ACORN_BASEPATH', Config::get('WP_CONTENT_DIR') . '/themes/sage');
```

In `sage/config/app.php` change:

```diff
-use Roots\Acorn\ServiceProvider;
+use Yard\BraveChild\ServiceProvider;
```

In `sage/functions.php` change:

```diff
-\Roots\Bootloader()->boot();
+$bootloader = \Roots\bootloader();
+$bootloader->getApplication()->bind(
+  \Roots\Acorn\Bootstrap\LoadConfiguration::class,
+  \Yare\BraveChild\Bootstrap\LoadConfiguration::class
+);
+$bootloader->boot();
```

Add any view composers you have to `config/view.php`:

```diff
-  'composers' => [],
+  'composers => [
+    'app' => App\View\Composers\App::class,
+    'comments' => App\View\Composers\Comments::class,
+    'post' => App\View\Composers\Post::class,
+  ],
```
