<?php

declare(strict_types=1);

namespace Yard\Nutshell\Http;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Router;
use Roots\Acorn\Http\Kernel as AcornKernel;

class Kernel extends AcornKernel
{
	public function __construct(Application $app, Router $router)
	{
		parent::__construct($app, $router);
		$this->middleware[] = \Spatie\Csp\AddCspHeaders::class;
	}
}
