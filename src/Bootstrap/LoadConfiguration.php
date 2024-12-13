<?php

declare(strict_types=1);

namespace Yard\SageChildThemeSupport\Bootstrap;

use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Foundation\Application;
use Roots\Acorn\Bootstrap\LoadConfiguration as AcornLoadConfiguration;
use Yard\SageChildThemeSupport\Config\Repository;

class LoadConfiguration extends AcornLoadConfiguration
{
	public function bootstrap(ApplicationContract $app)
	{
		parent::bootstrap($app);

		if (! is_child_theme()) {
			return;
		}

		//Swap config repository with extended version to allow unsetting of config values
		$app->instance('config', new Repository($app->get('config')->all()));

		/** @var Application */
		$childApp = clone $app;
		$childApp->useConfigPath(get_stylesheet_directory() . '/config');

		$this->loadChildConfigurationFiles($childApp, $app->get('config'));
	}

	public function loadChildConfigurationFiles(Application $childApp, Repository $repository): void
	{
		$files = $this->getConfigurationFiles($childApp);

 		foreach ($files as $key => $path) {
			$config = require $path;
			if (0 === count($config) ) {
				$repository->unset($key);

			} else {
				$repository->set($key, array_merge(
					$repository->get($key, []),
					$config
				));
			}
		}
	}
}
