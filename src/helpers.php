<?php

declare(strict_types=1);

namespace Yard\Nutshell;

use Roots\Acorn\Application;
use Sentry\Laravel\Integration;
use Throwable;

function bootloader(): Application
{
	$application = Application::configure()
		->withBindings([
			\Roots\Acorn\Bootstrap\LoadConfiguration::class => \Yard\Nutshell\Bootstrap\LoadConfiguration::class,
			\Roots\Acorn\Console\Kernel::class => \Yard\Nutshell\Console\Kernel::class,
			\Yard\Nutshell\Assets\Vite::class => \Illuminate\Foundation\Vite::class,
		])
		->withExceptions(function ($exceptions) {
			$exceptions->report(function (Throwable $e) {
				Integration::captureUnhandledException($e);
			});
		})
		->withMiddleware([
			\Spatie\Csp\AddCspHeaders::class,
		])
		->withRouting(wordpress: true)
		->boot()
		->usePublicPath(get_theme_file_path('public'));

	return $application;
}
