<?php

declare(strict_types=1);

namespace Yard\Nutshell\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use Roots\Acorn\Bootstrap\HandleExceptions as AcornHandleExceptions;
use Throwable;

class HandleExceptions extends AcornHandleExceptions
{
	private bool $reported = false;

	/**
	 * @var callable|null
	 */
	private $previousExceptionHandler;

	public function bootstrap(Application $app)
	{
		parent::bootstrap($app);

		if ($this->isDebug() && ! $this->hasHandler()) {
			return;
		}

		$this->previousExceptionHandler = set_exception_handler([$this, 'reportUncaughtException']);
		register_shutdown_function([$this, 'reportFatalError']);

		// WP_Fatal_Error_Handler::handle() (wp-includes/class-wp-fatal-error-handler.php) dies while rendering the
		// error template, so shutdown functions registered after it never run for that fatal.
		add_filter('wp_php_error_message', function ($message, $error) {
			$this->reportFatalError($error);

			return $message;
		}, 10, 2);
	}

	public function reportUncaughtException(Throwable $e): void
	{
		$this->reportOnce($e);

		if (null !== $this->previousExceptionHandler) {
			($this->previousExceptionHandler)($e);

			return;
		}

		throw $e;
	}

	/**
	 * @param array{type: int, message: string, file: string, line: int}|null $error
	 */
	public function reportFatalError(?array $error = null): void
	{
		$error ??= error_get_last();

		if (null === $error || ! $this->isFatal($error['type'])) {
			return;
		}

		$this->reportOnce($this->fatalErrorFromPhpError($error, 0));
	}

	private function reportOnce(Throwable $e): void
	{
		if ($this->reported) {
			return;
		}

		$this->reported = true;
		self::$reservedMemory = null;

		try {
			$this->getExceptionHandler()->report($e);
		} catch (Throwable) {
		}
	}
}
