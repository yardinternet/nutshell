<?php

declare(strict_types=1);

namespace Yard\Nutshell\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class InitializeAdminBar
{
	public function handle(Request $request, Closure $next): Response
	{
		_wp_admin_bar_init();

		return $next($request);
	}
}
