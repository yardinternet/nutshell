<?php

declare(strict_types=1);

namespace Yard\BraveChild\Bootstrap;

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
		/** @var Application*/
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
			$repository->set($key, array_merge(
				require $path,
				$repository->get($key, [])
			));
		}
	}
}
