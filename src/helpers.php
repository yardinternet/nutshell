<?php

declare(strict_types=1);

namespace Yard\SageChildThemeSupport;

use Illuminate\Foundation\Vite as FoundationVite;

use Roots\Acorn\Bootloader;
use Yard\SageChildThemeSupport\Assets\Vite;

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

	$bootloader->getApplication()->alias(Vite::class, FoundationVite::class);

	return $bootloader;
}
