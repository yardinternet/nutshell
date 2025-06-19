<?php

declare(strict_types=1);

namespace Yard\Nutshell\Config;

use Illuminate\Config\Repository as RepositoryBase;
use Illuminate\Support\Arr;

class Repository extends RepositoryBase
{
	/**
	 * @param array<string>|string $key
	 */
	public function unset(array|string $key): void
	{
		Arr::forget($this->items, $key);
	}
}
