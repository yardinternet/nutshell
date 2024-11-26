<?php

declare(strict_types=1);

namespace Yard\BraveChild;

use Roots\Acorn\DefaultProviders as AcornDefaultProviders;

class DefaultProviders extends AcornDefaultProviders
{
	protected array $acornProvidersReplacements = [
		\Roots\Acorn\View\ViewServiceProvider::class => \Yard\BraveChild\View\ViewServiceProvider::class,
	];

	public function __construct(?array $providers = null)
	{
		$this->acornProviders = array_map(
			fn (string $provider): string => str_replace(
				array_keys($this->acornProvidersReplacements),
				array_values($this->acornProvidersReplacements),
				$provider
			),
			$this->acornProviders
		);

		parent::__construct($providers);
	}
}
