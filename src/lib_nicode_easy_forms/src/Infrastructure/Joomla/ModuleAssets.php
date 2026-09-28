<?php
declare(strict_types=1);
namespace Nicode\EasyForms\Infrastructure\Joomla;

use Joomla\CMS\WebAsset\WebAssetManager;

/** Version transitive ES imports too: a query on the entry script is not inherited. */
final class ModuleAssets
{
    public static function register(WebAssetManager $manager, string $directory, string $baseUri): void
    {
        foreach (glob($directory . '/*.js') ?: [] as $file) {
            $name = 'com_nicode_easy_forms.module.' . basename($file, '.js');
            $uri = rtrim($baseUri, '/') . '/' . basename($file);
            $manager->registerAndUseScript($name, $uri, [
                'version' => hash_file('sha256', $file),
                'importmap' => true,
                'importmapName' => $uri,
            ], ['type' => 'module']);
        }
    }
}
