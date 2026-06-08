<?php

declare(strict_types=1);

namespace Yard\Nutshell;

use Illuminate\Foundation\Configuration\Exceptions;
use Roots\Acorn\Configuration\ApplicationBuilder;
use Roots\Acorn\Configuration\Middleware;
use Sentry\Laravel\Integration;
use Spatie\Csp\AddCspHeaders;
use Yard\Logging\Log;

class Application extends \Roots\Acorn\Application
{
	public function __construct($basePath = null)
	{
		parent::__construct($basePath);

		$this->singleton(
			\Roots\Acorn\Bootstrap\LoadConfiguration::class,
			\Yard\Nutshell\Bootstrap\LoadConfiguration::class,
		);

		$this->singleton(
			\Roots\Acorn\Console\Kernel::class,
			\Yard\Nutshell\Console\Kernel::class,
		);
	}

	public static function configure(?string $basePath = null): ApplicationBuilder
	{
		$application = parent::configure($basePath)
			->withMiddleware(function (Middleware $middleware) {
				$middleware->append(AddCspHeaders::class);
			})
			->withPaths(public: get_theme_file_path('public'))
			->withRouting(wordpress: true)
			->withExceptions(function (Exceptions $exceptions) {
				Integration::handles($exceptions);
			})
			->withProviders()
			->booted(function (self $application) {
				// Push Laravel logger to WordPress plugins
				do_action(Log::WP_ACTION_SET_LOGGER, $application->make('log'));
			});

		return $application;
	}
}
