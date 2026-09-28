<?php
declare(strict_types=1);
namespace NicodeFixture\Plugin\Easyforms\ProviderFixture\Extension;
defined('_JEXEC') or die;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;
use Nicode\EasyForms\Infrastructure\Joomla\ProviderRegistrationEvent;
final class ProviderFixture extends CMSPlugin implements SubscriberInterface
{
    public static array $lifecycle = [];
    public static function getSubscribedEvents(): array
    {
        $events = ['onEasyFormsRegisterProviders' => 'registerProviders'];
        foreach (\Nicode\EasyForms\Infrastructure\Joomla\LifecycleEvent::PHASES as $phase) { $events['onEasyForms' . $phase] = 'observe'; }
        return $events;
    }
    public function observe(\Nicode\EasyForms\Infrastructure\Joomla\LifecycleEvent $event): void { self::$lifecycle[] = ['phase' => $event->phase, 'context' => $event->context]; }
    public function registerProviders(ProviderRegistrationEvent $event): void
    {
        if ($event->kind === 'fields') { $event->registry->register(new \NicodeFixture\Plugin\Easyforms\ProviderFixture\Provider\FixtureField()); }
        if ($event->kind === 'renderers') { $event->registry->register('fixture.upper', new \NicodeFixture\Plugin\Easyforms\ProviderFixture\Provider\FixtureRenderer()); }
        if ($event->kind === 'operators') {
            $event->registry->register(new \NicodeFixture\Plugin\Easyforms\ProviderFixture\Provider\FixtureOperator());
            $app = $this->getApplication();
            if ($app instanceof \Joomla\CMS\Application\CMSWebApplicationInterface && $app->getDocument() !== null) {
                $app->getDocument()->getWebAssetManager()->registerScript('fixture.browser', 'plg_easyforms_providerfixture/fixture.js', ['version' => '1.2.0'], ['type' => 'module']);
                $app->getDocument()->getWebAssetManager()->registerStyle('fixture.browser', 'plg_easyforms_providerfixture/fixture.css', ['version' => '1.2.0']);
            }
        }
        if ($event->kind === 'sources') { $event->registry->register(new \NicodeFixture\Plugin\Easyforms\ProviderFixture\Provider\FixtureSource()); }
        if ($event->kind === 'search') { $event->registry->register(new \NicodeFixture\Plugin\Easyforms\ProviderFixture\Provider\FixtureSearch($event->registry->get('sql'))); }
    }
}
