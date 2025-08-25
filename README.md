# Nutshell: Enhanced Acorn Support for WordPress Themes

[![Code Style](https://github.com/yardinternet/skeleton-package/actions/workflows/format-php.yml/badge.svg?no-cache)](https://github.com/yardinternet/skeleton-package/actions/workflows/format-php.yml)
[![PHPStan](https://github.com/yardinternet/skeleton-package/actions/workflows/phpstan.yml/badge.svg?no-cache)](https://github.com/yardinternet/skeleton-package/actions/workflows/phpstan.yml)

**Nutshell** is a feature-rich package designed to extend [Acorn](https://roots.io/acorn/) for WordPress themes. It provides a flexible foundation for advanced theme development, including configuration inheritance, Sentry integration, Vite asset support, and more.

## Features

- **Child Theme Configuration Inheritance**:
  - Allows child themes to override parent configuration files without directory scans.
  - Uses a custom configuration repository to support unsetting and merging config values.
- **Vite Asset Support**:
  - Integrates with Vite for modern asset bundling and hot reloading.
- **Sentry Integration**:
  - Seamless error reporting via Sentry for Laravel.
- **Custom View Composers**:
  - Manual registration of view composers for fine-grained control.
- **Custom Console Commands**:
  - Register custom Artisan commands via configuration.

## Requirements

- PHP >= 8.1
- [Acorn](https://github.com/roots/acorn) ^4.3
- Composer

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
         * Requires PHP:       8.2
         * Requires at least:  5.9
         */
         ```

   - Place your configuration files in `config/` within your child theme directory. These will be merged with the parent configuration where child theme configuration takes precedence. To unset a configuration option from the parent theme in the child theme you can pass an empty array for that configuration option.

2. **Update Acorn Bootloader**

   - In your theme's `functions.php`, use the `Yard\Nutshell\bootloader()` helper to bootstrap Acorn with Nutshell's enhancements.

      ```diff
      -\Roots\Acorn\Application::configure()->boot();
      +define('ACORN_BASEPATH', __DIR__);
      +\Yard\Nutshell\bootloader();
      ```

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

6. **Vite Integration**
   - Vite is enabled by default. Use the provided `Yard\Nutshell\Assets\Vite` class for asset management.

7. **Sentry Integration**
   - Sentry is automatically integrated if `sentry/sentry-laravel` is installed and configured.

## Usage

- **Configuration Inheritance**: Any config file in your child theme's `config/` directory will override the parent. Empty config files will unset the corresponding configuration.
- **View Composers**: Register all composers manually in `config/view.php`.
- **Console Commands**: Register all commands manually in `config/console.php`.
- **Vite**: Use the `@vite` directive or helper as usual; Nutshell ensures correct asset paths.
- **Sentry**: Errors and exceptions are reported to Sentry automatically.

## Contributing

Pull requests are welcome! Please ensure code style and tests pass before submitting.
