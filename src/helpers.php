<?php

declare(strict_types=1);

namespace Yard\Nutshell;

use Roots\Acorn\Application;
use Roots\Acorn\Configuration\ApplicationBuilder;
use Roots\Acorn\Configuration\Exceptions;
use Roots\Acorn\Configuration\Middleware;
use Sentry\Laravel\Integration;
use Spatie\Csp\AddCspHeaders;

function bootloader(?string $basePath = null): ApplicationBuilder
{
	return Application::configure($basePath)
		->withBindings([
			\Roots\Acorn\Bootstrap\LoadConfiguration::class => \Yard\Nutshell\Bootstrap\LoadConfiguration::class,
			\Roots\Acorn\Console\Kernel::class => \Yard\Nutshell\Console\Kernel::class,
		])
		->withExceptions(function (Exceptions $exceptions) {
			$exceptions->report(function (\Throwable $e) {
				Integration::captureUnhandledException($e);
			});
		})
		->withMiddleware(function (Middleware $middleware) {
			$middleware->append(AddCspHeaders::class);
		})
		->withRouting(wordpress: true)
		->withPaths(public: get_theme_file_path('public'));
}
