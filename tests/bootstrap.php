<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

define('WP_DEBUG', false);
define('WP_CONTENT_DIR', __DIR__);

$GLOBALS['wp_test_filters'] = [];

function add_filter(string $hook, callable $callback, int $priority = 10, int $acceptedArgs = 1): bool
{
	$GLOBALS['wp_test_filters'][$hook][] = $callback;

	return true;
}

function apply_filters(string $hook, mixed $value, mixed ...$args): mixed
{
	foreach ($GLOBALS['wp_test_filters'][$hook] ?? [] as $callback) {
		$value = $callback($value, ...$args);
	}

	return $value;
}
