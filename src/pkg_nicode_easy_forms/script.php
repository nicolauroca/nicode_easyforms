<?php
declare(strict_types=1);
defined('_JEXEC') or die;

use Joomla\CMS\Installer\{InstallerAdapter, InstallerScriptInterface};
use Joomla\Database\{DatabaseAwareInterface, DatabaseAwareTrait};

/** Check prerequisites before the package changes any child extension. */
return new class implements InstallerScriptInterface, DatabaseAwareInterface {
    use DatabaseAwareTrait;
    public function install(InstallerAdapter $adapter): bool { return true; }
    public function update(InstallerAdapter $adapter): bool { return true; }
    public function uninstall(InstallerAdapter $adapter): bool { return true; }
    public function postflight(string $type, InstallerAdapter $adapter): bool { return true; }
    public function preflight(string $type, InstallerAdapter $adapter): bool
    {
        if ($type === 'uninstall') {
            $guard = JPATH_ADMINISTRATOR . '/components/com_nicode_easy_forms/purgeguard.php';
            if (is_file($guard)) { require_once $guard; NicodeEasyFormsPurgeGuard::check($this->getDatabase()); }
            return true;
        }
        $guard = JPATH_ADMINISTRATOR . '/components/com_nicode_easy_forms/purgeguard.php';
        if (is_file($guard)) {
            require_once $guard;
            if (NicodeEasyFormsPurgeGuard::check($this->getDatabase())) { throw new RuntimeException('Complete the prepared EasyForms uninstall before installing another package version.'); }
        }
        if (version_compare(PHP_VERSION, '8.3.0', '<') || version_compare(JVERSION, '6.0.0', '<')) { throw new RuntimeException('Nicode EasyForms requires PHP 8.3 and Joomla 6 or newer.'); }
        foreach (['mbstring', 'intl', 'bcmath', 'fileinfo', 'curl', 'zip'] as $extension) {
            if (!extension_loaded($extension)) { throw new RuntimeException('Nicode EasyForms requires PHP extension: ' . $extension); }
        }
        $db = $this->getDatabase(); $type = $db->getServerType(); $version = $db->getVersion();
        $maria = stripos($version, 'mariadb') !== false;
        if (!in_array($type, ['mysql', 'postgresql'], true) || version_compare($version, $type === 'postgresql' ? '14.0' : ($maria ? '10.6.0' : '8.0.13'), '<')) { throw new RuntimeException('Nicode EasyForms requires MySQL 8.0.13, MariaDB 10.6 or PostgreSQL 14 or newer.'); }
        return true;
    }
};
