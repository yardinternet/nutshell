<?php

declare(strict_types=1);

namespace Yard\SageChildThemeSupport\Bootstrap;

use Illuminate\Contracts\Config\Repository as RepositoryContract;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Foundation\Application;
use Roots\Acorn\Bootstrap\LoadConfiguration as AcornLoadConfiguration;

class LoadConfiguration extends AcornLoadConfiguration
{
	public function bootstrap(ApplicationContract $app)
	{
		parent::bootstrap($app);

		if (! is_child_theme()) {
			return;
		}
		/** @var Application */
		$childApp = clone $app;
		$childApp->useConfigPath(get_stylesheet_directory() . '/config');

		$this->loadChildConfigurationFiles($childApp, $app->get('config'));
	}

	public function loadChildConfigurationFiles(Application $app, RepositoryContract $repository): void
	{
		$files = $this->getConfigurationFiles($app);

		if (! isset($files['app']) && ! $repository->has('app')) {
			$repository->set('app', require dirname(__DIR__, 4).'/config/app.php');
		}

		foreach ($files as $key => $path) {
			$config = require $path;

			if (0 === count($config)) {
				$this->unsetConfig($repository, $key);
			} else {
				$repository->set($key, array_merge(
					$repository->get($key, []),
					$config
				));
			}
		}
	}

	private function unsetConfig(RepositoryContract $repository, string $key): void
	{
		$parentConfig = $repository->get($this->withoutLastSegment($key), []);
		unset($parentConfig[$this->lastSegment($key)]);
		$repository->set($this->withoutLastSegment($key), $parentConfig);
	}

	private function withoutLastSegment(string $key): string
	{
		$segments = explode('.', $key);

		array_pop($segments);

		return implode('.', $segments);
	}

	private function lastSegment(string $key): string
	{
		$segments = explode('.', $key);

		return end($segments);
	}
}
