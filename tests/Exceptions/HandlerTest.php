<?php

declare(strict_types=1);

use Sentry\Event;
use Sentry\EventHint;
use Yard\Nutshell\Exceptions\Handler;

it('drops a second Sentry event for an exception that was already captured', function () {
	$exception = new RuntimeException('reported');

	expect(Handler::dropDuplicate($event = Event::createEvent(), EventHint::fromArray(['exception' => $exception])))->toBe($event)
		->and(Handler::dropDuplicate(Event::createEvent(), EventHint::fromArray(['exception' => $exception])))->toBeNull();
});

it('keeps Sentry events for different exceptions', function () {
	Handler::dropDuplicate(Event::createEvent(), EventHint::fromArray(['exception' => new RuntimeException('first')]));

	expect(Handler::dropDuplicate($event = Event::createEvent(), EventHint::fromArray(['exception' => new RuntimeException('second')])))->toBe($event);
});

it('keeps Sentry events without an exception', function () {
	$hint = new EventHint();

	expect(Handler::dropDuplicate($event = Event::createEvent(), $hint))->toBe($event)
		->and(Handler::dropDuplicate($event, $hint))->toBe($event);
});
