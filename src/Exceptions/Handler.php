<?php

declare(strict_types=1);

namespace Yard\SageChildThemeSupport\Exceptions;

use Roots\Acorn\Exceptions\Handler as AcornHandler;
use Sentry\Laravel\Integration;
use Throwable;

class Handler extends AcornHandler
{
	public function register()
	{
		$this->reportable(function (Throwable $e) {
			Integration::captureUnhandledException($e);
		});
	}
}
