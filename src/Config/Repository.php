<?php

declare(strict_types=1);

namespace Yard\SageChildThemeSupport\Config;

use Illuminate\Config\Repository as RepositoryBase;
use Illuminate\Support\Arr;

class Repository extends RepositoryBase
{
	public function unset($key)
	{
		Arr::forget($this->items, $key);
	}
}
