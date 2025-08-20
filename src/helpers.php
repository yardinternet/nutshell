<?php

declare(strict_types=1);

namespace Yard\Nutshell;

use Roots\Acorn\Application;

function bootloader(): Application
{
	$application = Application::configure()
		->withProviders()
		->withBindings([
			\Roots\Acorn\Bootstrap\LoadConfiguration::class => \Yard\Nutshell\Bootstrap\LoadConfiguration::class,
			\Roots\Acorn\Console\Kernel::class => \Yard\Nutshell\Console\Kernel::class,
			\Roots\Acorn\Http\Kernel::class => \Yard\Nutshell\Http\Kernel::class,
			\Roots\Acorn\Exceptions\Handler::class => \Yard\Nutshell\Exceptions\Handler::class,
			\Yard\Nutshell\Assets\Vite::class => \Illuminate\Foundation\Vite::class,
		])
		->withRouting(wordpress: true)
		->boot()
		->usePublicPath(get_theme_file_path('public'));

	return $application;
}
