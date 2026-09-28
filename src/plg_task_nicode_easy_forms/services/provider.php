<?php
declare(strict_types=1);
defined('_JEXEC') or die;
use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\{Container, ServiceProviderInterface};
use Nicode\Plugin\Task\EasyForms\Extension\EasyForms;
return new class implements ServiceProviderInterface {
    public function register(Container $container)
    {
        $container->set(PluginInterface::class, static function (): EasyForms {
            $plugin = new EasyForms((array) PluginHelper::getPlugin('task', 'nicode_easy_forms'));
            $plugin->setApplication(Factory::getApplication());
            return $plugin;
        });
    }
};
