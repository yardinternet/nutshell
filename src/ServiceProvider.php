<?php

declare(strict_types=1);

namespace Yard\BraveChild;

use Illuminate\Support\ServiceProvider as ServiceProviderBase;

final class ServiceProvider extends ServiceProviderBase
{
	/**
	 * Get the default providers for a Acorn application.
	 *
	 * @return \Roots\Acorn\DefaultProviders
	 */
	public static function defaultProviders()
	{
		return new DefaultProviders();
	}
}
