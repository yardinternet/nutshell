<?php

declare(strict_types=1);

namespace Yard\Nutshell\Exceptions;

use Roots\Acorn\Exceptions\Handler as AcornHandler;
use Sentry\Event;
use Sentry\EventHint;
use Sentry\Laravel\Integration;
use Sentry\State\Scope;
use Throwable;
use WeakMap;

class Handler extends AcornHandler
{
	/**
	 * @var WeakMap<object, bool>|null
	 */
	private static ?WeakMap $capturedExceptions = null;

	public function register()
	{
		$this->reportable(function (Throwable $e) {
			Integration::captureUnhandledException($e);
		});

		if (null === self::$capturedExceptions) {
			self::$capturedExceptions = new WeakMap();
			Scope::addGlobalEventProcessor(static fn (Event $event, EventHint $hint): ?Event => self::dropDuplicate($event, $hint));
		}
	}

	public static function dropDuplicate(Event $event, EventHint $hint): ?Event
	{
		if (null === $hint->exception) {
			return $event;
		}

		$capturedExceptions = self::$capturedExceptions ??= new WeakMap();

		if (isset($capturedExceptions[$hint->exception])) {
			return null;
		}

		$capturedExceptions[$hint->exception] = true;

		return $event;
	}
}
