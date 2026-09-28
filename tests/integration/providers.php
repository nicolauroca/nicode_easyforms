<?php
declare(strict_types=1);
use Joomla\Event\Dispatcher;
use Nicode\EasyForms\Infrastructure\Joomla\{ProviderDiscovery, ProviderRegistrationEvent};
use Nicode\EasyForms\Registry\DataSourceRegistry;
use Nicode\EasyForms\DataSource\StaticDataSource;

test('typed Joomla provider registration closes registries after plugin initialization', function (): void {
    $dispatcher = new Dispatcher(); $loaded = 0;
    $dispatcher->addListener('onEasyFormsRegisterProviders', static function (ProviderRegistrationEvent $event): void {
        same('sources', $event->kind); same($event->registry, $event->getArgument('registry'));
        $event->registry->register(new StaticDataSource());
    });
    $discovery = new ProviderDiscovery($dispatcher, static function () use (&$loaded): void { $loaded++; });
    $registry = new DataSourceRegistry(); same($registry, $discovery->complete('sources', $registry));
    same(1, $loaded); same(true, $registry->has('static'));
    raises(LogicException::class, fn () => $registry->register(new StaticDataSource('option_set')));
});

test('a failed provider plugin cannot leave a writable partially initialized registry', function (): void {
    $dispatcher = new Dispatcher(); $registry = new DataSourceRegistry();
    $dispatcher->addListener('onEasyFormsRegisterProviders', static function (): void { throw new DomainException('fixture failure'); });
    $discovery = new ProviderDiscovery($dispatcher, static fn () => null);
    raises(DomainException::class, fn () => $discovery->complete('sources', $registry));
    raises(LogicException::class, fn () => $registry->register(new StaticDataSource()));
});
