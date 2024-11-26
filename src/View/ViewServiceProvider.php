<?php

declare(strict_types=1);

namespace Yard\BraveChild\View;

use Illuminate\Support\Arr;
use Roots\Acorn\View\ViewServiceProvider as ViewViewServiceProvider;

class ViewServiceProvider extends ViewViewServiceProvider
{

	public function attachComposers()
	{
		$composers = $this->app->config['view.composers'];

		if (is_array($composers) && Arr::isAssoc($composers)) {
			foreach ($composers as $composer) {
				$this->view()->composer($composer::views(), $composer);
			}
		}
	}
}
