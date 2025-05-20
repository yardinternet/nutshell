<?php

declare(strict_types=1);

namespace Yard\SageChildThemeSupport;

use Roots\Acorn\Bootloader;

function bootloader(): Bootloader
{
	$bootloader = \Roots\bootloader();
	$bootloader->getApplication()->bind(
		\Roots\Acorn\Bootstrap\LoadConfiguration::class,
		\Yard\SageChildThemeSupport\Bootstrap\LoadConfiguration::class
	);
	$bootloader->getApplication()->bind(
		\Roots\Acorn\Console\Kernel::class,
		\Yard\SageChildThemeSupport\Console\Kernel::class
	);

	$bootloader->getApplication()->alias(
		\Yard\SageChildThemeSupport\Assets\Vite::class,
		\Illuminate\Foundation\Vite::class
	);

	$bootloader->getApplication()->usePublicPath(get_theme_file_path('public'));

	return $bootloader;
}
