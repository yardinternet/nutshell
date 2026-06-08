<?php

declare(strict_types=1);

namespace Yard\Nutshell;

use Roots\Acorn\DefaultProviders as AcornDefaultProviders;

class DefaultProviders extends AcornDefaultProviders
{
	/**
	 * @var array<class-string>
	 */
	protected array $acornProvidersReplacements = [
		\Roots\Acorn\View\ViewServiceProvider::class => \Yard\Nutshell\View\ViewServiceProvider::class,
	];

	/**
	 * @param array<class-string>|null $providers
	 */
	public function __construct(?array $providers = null)
	{
		parent::__construct($providers);
		$this->replace($this->acornProvidersReplacements);
	}
}
