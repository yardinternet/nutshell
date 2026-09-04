<?php

declare(strict_types=1);

namespace Yard\Nutshell\View;

use Illuminate\Support\Arr;
use Roots\Acorn\View\Composer;
use Roots\Acorn\View\ViewServiceProvider as ViewViewServiceProvider;

class ViewServiceProvider extends ViewViewServiceProvider
{
	public function attachComposers()
	{
		/** @var array<Composer> */
		$composers = $this->app->get('config')->get('view.composers', []);

		if (Arr::isAssoc($composers)) {
			foreach ($composers as $composer) {
				// @phpstan-ignore method.notFound
				$this->view()->composer($composer::views(), $composer);
			}
		}
	}
}
