<?php

declare(strict_types=1);

namespace Yard\BraveChild\Bootstrap;

use Illuminate\Contracts\Config\Repository as RepositoryContract;
use Illuminate\Contracts\Foundation\Application;
use Roots\Acorn\Bootstrap\LoadConfiguration as AcornLoadConfiguration;

class LoadConfiguration extends AcornLoadConfiguration
{
	public function bootstrap(Application $app)
	{
		parent::bootstrap($app);

		if (! is_child_theme()) {
			return;
		}

		$childApp = clone $app;
		$childApp->useConfigPath(get_stylesheet_directory() . '/config');

		$this->loadChildConfigurationFiles($childApp, $app['config']);
	}

	public function loadChildConfigurationFiles(Application $app, RepositoryContract $repository)
	{
		$files = $this->getConfigurationFiles($app);

		if (! isset($files['app']) && ! $repository->has('app')) {
			$repository->set('app', require dirname(__DIR__, 4).'/config/app.php');
		}

		foreach ($files as $key => $path) {
			$repository->set($key, require $path);
		}
	}
}
