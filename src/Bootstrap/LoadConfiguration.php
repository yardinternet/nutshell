<?php

declare(strict_types=1);

namespace Yard\Nutshell\Bootstrap;

use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Foundation\Application;
use Roots\Acorn\Bootstrap\LoadConfiguration as AcornLoadConfiguration;
use Yard\Nutshell\Config\Repository;

class LoadConfiguration extends AcornLoadConfiguration
{
	public function bootstrap(ApplicationContract $app)
	{
		parent::bootstrap($app);

		if (! is_child_theme() || ! is_dir(get_stylesheet_directory() . '/config')) {
			return;
		}

		//Swap config repository with extended version to allow unsetting of config values
		$app->instance('config', new Repository($app->get('config')->all()));

		/** @var Application */
		$childApp = clone $app;
		$childApp->useConfigPath(get_stylesheet_directory() . '/config');

		// Deferred until every provider has registered, so child config also overrides package defaults.
		// Example: a package's register() adds five defaults under `shop.labels`; applying a child theme's
		// single-key override earlier would let that shallow merge drop the other four.
		$app->booting(function () use ($app, $childApp): void {
			$this->loadChildConfigurationFiles($childApp, $app->get('config'));
		});
	}

	public function loadChildConfigurationFiles(Application $childApp, Repository $repository): void
	{
		$files = $this->getConfigurationFiles($childApp);

		foreach ($files as $key => $path) {
			$config = require $path;
			if (0 === count($config)) {
				$repository->unset($key);
			} else {
				$repository->set($key, $this->mergeRecursively(
					$repository->get($key, []),
					$config
				));
			}
		}
	}

	/**
	 * Merges nested arrays key by key, so a child theme overriding one nested value
	 * keeps the parent's siblings instead of replacing the whole array.
	 *
	 * @param array<string, mixed> $base
	 * @param array<string, mixed> $overrides
	 *
	 * @return array<string, mixed>
	 */
	private function mergeRecursively(array $base, array $overrides): array
	{
		foreach ($overrides as $key => $value) {
			$base[$key] = is_array($value) && is_array($base[$key] ?? null) && ! array_is_list($value)
				? $this->mergeRecursively($base[$key], $value)
				: $value;
		}

		return $base;
	}
}
