<?php

declare(strict_types=1);

use Illuminate\Config\Repository;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Roots\Acorn\Application;
use Symfony\Component\ErrorHandler\Error\FatalError;
use Yard\Nutshell\Bootstrap\HandleExceptions;

function bootstrapExceptionHandling(bool $debug): HandleExceptions
{
	$app = new Application(sys_get_temp_dir());
	$app->instance('config', new Repository(['app' => ['debug' => $debug]]));
	$app->instance(ExceptionHandler::class, $handler = new class() implements ExceptionHandler {
		/** @var array<Throwable> */
		public array $reported = [];

		public function report(Throwable $e)
		{
			$this->reported[] = $e;
		}

		public function shouldReport(Throwable $e)
		{
			return true;
		}

		public function render($request, Throwable $e)
		{
		}

		public function renderForConsole($output, Throwable $e)
		{
		}
	});

	$bootstrapper = new HandleExceptions();
	$bootstrapper->bootstrap($app);

	test()->reportedExceptions = fn (): array => $handler->reported;

	return $bootstrapper;
}

function currentExceptionHandler(): ?callable
{
	$handler = set_exception_handler(null);
	set_exception_handler($handler);

	return $handler;
}

beforeEach(function () {
	$GLOBALS['wp_test_filters'] = [];
	$this->previousExceptionHandler = set_exception_handler(null);
	set_exception_handler($this->previousExceptionHandler);
});

afterEach(function () {
	set_exception_handler($this->previousExceptionHandler);
	HandleExceptions::forgetApp();
});

it('reports an uncaught exception and rethrows it for WordPress when debug is off', function () {
	$bootstrapper = bootstrapExceptionHandling(debug: false);
	$exception = new RuntimeException('uncaught');

	expect(currentExceptionHandler())->toBe([$bootstrapper, 'reportUncaughtException'])
		->and(fn () => $bootstrapper->reportUncaughtException($exception))->toThrow($exception)
		->and(($this->reportedExceptions)())->toBe([$exception]);
});

it('passes an uncaught exception on to a previously registered exception handler', function () {
	$passedOn = null;
	set_exception_handler(function (Throwable $e) use (&$passedOn) {
		$passedOn = $e;
	});
	$bootstrapper = bootstrapExceptionHandling(debug: false);
	$exception = new RuntimeException('uncaught');

	$bootstrapper->reportUncaughtException($exception);

	expect($passedOn)->toBe($exception)
		->and(($this->reportedExceptions)())->toBe([$exception]);
});

it('reports a fatal error', function () {
	$bootstrapper = bootstrapExceptionHandling(debug: false);

	$bootstrapper->reportFatalError(['type' => E_ERROR, 'message' => 'Allowed memory size exhausted', 'file' => __FILE__, 'line' => 1]);

	expect(($this->reportedExceptions)())->toHaveCount(1)
		->and(($this->reportedExceptions)()[0])->toBeInstanceOf(FatalError::class)
		->and(($this->reportedExceptions)()[0]->getMessage())->toBe('Allowed memory size exhausted');
});

it('ignores errors that are not fatal', function () {
	$bootstrapper = bootstrapExceptionHandling(debug: false);

	$bootstrapper->reportFatalError(['type' => E_WARNING, 'message' => 'warning', 'file' => __FILE__, 'line' => 1]);

	expect(($this->reportedExceptions)())->toBe([]);
});

it('reports the fatal error WordPress renders its error template for', function () {
	bootstrapExceptionHandling(debug: false);
	$error = ['type' => E_ERROR, 'message' => 'fatal', 'file' => __FILE__, 'line' => 1];

	expect(apply_filters('wp_php_error_message', 'message', $error))->toBe('message')
		->and(($this->reportedExceptions)())->toHaveCount(1);
});

it('does not report the fatal error PHP raises for the rethrown exception a second time', function () {
	$bootstrapper = bootstrapExceptionHandling(debug: false);
	$exception = new RuntimeException('uncaught');

	try {
		$bootstrapper->reportUncaughtException($exception);
	} catch (RuntimeException) {
	}
	$bootstrapper->reportFatalError(['type' => E_ERROR, 'message' => 'Uncaught RuntimeException: uncaught', 'file' => __FILE__, 'line' => 1]);

	expect(($this->reportedExceptions)())->toBe([$exception]);
});

it('leaves exception handling to Acorn when debug is on', function () {
	$bootstrapper = bootstrapExceptionHandling(debug: true);
	restore_error_handler();

	expect(currentExceptionHandler())->toBe([$bootstrapper, 'handleException'])
		->and($GLOBALS['wp_test_filters'])->toBe([]);
});
