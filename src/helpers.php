<?php

declare(strict_types=1);

namespace Yard\Nutshell;

use Roots\Acorn\Bootloader;

function bootloader(): Bootloader
{
	$bootloader = \Roots\bootloader();

	$bootloader->getApplication()->bind(
		\Roots\Acorn\Bootstrap\LoadConfiguration::class,
		\Yard\Nutshell\Bootstrap\LoadConfiguration::class
	);

	$bootloader->getApplication()->bind(
		\Roots\Acorn\Console\Kernel::class,
		\Yard\Nutshell\Console\Kernel::class
	);

	$bootloader->getApplication()->bind(
		\Roots\Acorn\Exceptions\Handler::class,
		\Yard\Nutshell\Exceptions\Handler::class
	);

	$bootloader->getApplication()->alias(
		\Yard\Nutshell\Assets\Vite::class,
		\Illuminate\Foundation\Vite::class
	);

	$bootloader->getApplication()->usePublicPath(get_theme_file_path('public'));

	return $bootloader;
}
