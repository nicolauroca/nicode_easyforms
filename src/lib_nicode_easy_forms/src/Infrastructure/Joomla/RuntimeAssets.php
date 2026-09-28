<?php
declare(strict_types=1);
namespace Nicode\EasyForms\Infrastructure\Joomla;

use Joomla\CMS\Application\CMSWebApplicationInterface;

final class RuntimeAssets
{
    public static function load(CMSWebApplicationInterface $app): void
    {
        $app->allowCache(false);
        $app->setHeader('Cache-Control', 'private, no-store, max-age=0', true);
        $manager = $app->getDocument()->getWebAssetManager();
        $manager->getRegistry()->addExtensionRegistryFile('com_nicode_easy_forms');
        ModuleAssets::register($manager, JPATH_ROOT . '/media/com_nicode_easy_forms/js', \Joomla\CMS\Uri\Uri::root() . 'media/com_nicode_easy_forms/js');
        $manager->useStyle('com_nicode_easy_forms.styles')->useScript('com_nicode_easy_forms.runtime');
    }
}
