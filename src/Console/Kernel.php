<?php

declare(strict_types=1);

namespace Yard\SageChildThemeSupport\Console;

use Roots\Acorn\Console\Kernel as AcornKernel;

class Kernel extends AcornKernel
{
	public function commands()
	{
		/** @var array<class-string> */
		$commands = config('console.commands', []);

		foreach ($commands as $command) {
			$this->getArtisan()->resolve($command);
		}
	}
}
