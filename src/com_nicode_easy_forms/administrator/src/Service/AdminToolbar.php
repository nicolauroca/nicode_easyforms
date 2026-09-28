<?php
declare(strict_types=1);
namespace Nicode\Component\EasyForms\Administrator\Service;
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\{Toolbar, ToolbarHelper};

/** Native Joomla actions backed by the existing ACL/CSRF-protected controllers. */
final class AdminToolbar
{
    public static function build(string $view, array $data, string $kind = 'email'): void
    {
        $labels = ['dashboard' => 'DASH_TITLE', 'forms' => 'FORMS', 'editor' => 'EDIT_FORM', 'submissions' => 'SUBMISSIONS', 'submission' => 'SUBMISSIONS', 'jobs' => 'JOBS', 'health' => 'HEALTH', 'logs' => 'TECHNICAL_LOG', 'audit' => 'AUDIT', 'resources' => 'OPTION_SETS', 'optionset' => 'OPTION_SETS', 'templates' => $kind === 'form' ? 'FORM_TEMPLATES' : 'EMAIL_TEMPLATES', 'emailtemplate' => 'EMAIL_TEMPLATES', 'datasources' => 'DATA_SOURCES', 'datasource' => 'DATA_SOURCES', 'purge' => 'PURGE_TITLE'];
        ToolbarHelper::title('Nicode EasyForms: ' . Text::_('COM_NICODE_EASY_FORMS_' . $labels[$view]), 'file-alt');
        $toolbar = Toolbar::getInstance('toolbar');
        if ($view === 'forms' && $data['canCreate']) {
            self::action($toolbar, 'new', 'COM_NICODE_EASY_FORMS_NEW', 'plus', ['data-nef-reveal' => '[data-nef-create]'], 'btn btn-success button-new');
            self::action($toolbar, 'import', 'COM_NICODE_EASY_FORMS_DEFINITION_IMPORT', 'upload', ['data-nef-transfer' => 'import']);
        }
        if ($view === 'editor') {
            self::command($toolbar, 'save', 'SAVE', 'save', 'btn btn-success button-apply');
            if ($data['canPublish']) { self::command($toolbar, 'publish', 'PUBLISH', 'check'); }
            self::action($toolbar, 'preview', 'JGLOBAL_PREVIEW', 'eye', ['data-nef-command' => 'preview']);
            $more = $toolbar->dropdownButton('nef-actions', 'COM_NICODE_EASY_FORMS_TOOLBAR_MORE')->icon('icon-ellipsis-h')->buttonClass('btn btn-primary')->toggleSplit(false)->getChildToolbar();
            self::command($more, 'history', 'VERSIONS', 'history');
            if ($data['canCreate']) { self::command($more, 'duplicate', 'DUPLICATE', 'copy'); }
            foreach (['export' => 'download', 'import' => 'upload'] as $operation => $icon) { self::action($more, $operation, 'COM_NICODE_EASY_FORMS_DEFINITION_' . strtoupper($operation), $icon, ['data-nef-transfer' => $operation]); }
            if ($data['canResourceEdit']) { self::action($more, 'template', 'COM_NICODE_EASY_FORMS_TEMPLATE_CAPTURE', 'copy', ['data-nef-template-capture' => '']); }
            if ($data['canPublish']) {
                self::command($more, 'unpublish', 'UNPUBLISH', 'times');
                self::command($more, 'archive', 'ARCHIVE_FORM', 'archive');
                if ($data['canTrash']) { self::command($more, 'trash', 'TRASH_FORM', 'trash'); }
            }
            if ($data['canDelete']) { self::action($more, 'delete', 'COM_NICODE_EASY_FORMS_DELETE_FORM', 'trash', ['data-nef-command' => 'delete'] + (in_array($data['form']['state'], ['trashed', 'deleting'], true) ? [] : ['hidden' => true])); }
        }
        $editForms = ['optionset' => '[data-nef-resource-edit]', 'datasource' => '[data-nef-source-resource] form', 'emailtemplate' => '[data-nef-email-template] form'];
        if (isset($editForms[$view]) && ($view === 'optionset' ? $data['can_edit'] : $data['canResourceEdit'])) { self::action($toolbar, 'save', 'JSAVE', 'save', ['data-nef-submit' => $editForms[$view]], 'btn btn-success'); }
        if ($view !== 'dashboard') {
            $parent = ['editor' => 'forms', 'submission' => 'submissions', 'optionset' => 'resources', 'emailtemplate' => 'templates', 'datasource' => 'datasources'][$view] ?? 'dashboard';
            self::link($toolbar, 'back', 'COM_NICODE_EASY_FORMS_TOOLBAR_BACK', $parent, 'arrow-left', $data['toolbarReturnParams'] ?? []);
        }
        $navigation = $toolbar->dropdownButton('nef-navigation', 'COM_NICODE_EASY_FORMS_TOOLBAR_NAVIGATION')->icon('icon-compass')->buttonClass('btn btn-primary')->toggleSplit(false)->getChildToolbar();
        foreach (['dashboard', 'forms', 'submissions', 'jobs'] as $destination) { self::link($navigation, $destination, 'COM_NICODE_EASY_FORMS_' . $labels[$destination], $destination); }
        if ($data['canResources']) {
            foreach (['resources', 'datasources'] as $destination) { self::link($navigation, $destination, 'COM_NICODE_EASY_FORMS_' . $labels[$destination], $destination); }
            self::link($navigation, 'email-templates', 'COM_NICODE_EASY_FORMS_EMAIL_TEMPLATES', 'templates', 'envelope', ['kind' => 'email']);
            self::link($navigation, 'form-templates', 'COM_NICODE_EASY_FORMS_FORM_TEMPLATES', 'templates', 'file-alt', ['kind' => 'form']);
        }
        if ($data['canDiagnose']) { foreach (['health', 'logs', 'audit'] as $destination) { self::link($navigation, $destination, 'COM_NICODE_EASY_FORMS_' . $labels[$destination], $destination); } }
        if ($data['canPurge']) { self::link($navigation, 'purge', 'COM_NICODE_EASY_FORMS_PURGE_TITLE', 'purge', 'trash'); }
        if ($data['canConfigure']) { ToolbarHelper::preferences('com_nicode_easy_forms'); }
        $toolbar->linkButton('nef-help', 'JHELP')->buttonClass('btn btn-info button-help')->url('https://github.com/nicolauroca/nicode_easyforms/blob/main/docs/ADMIN_USER_GUIDE.md')->target('_blank')->attributes(['rel' => 'noopener noreferrer'])->icon('icon-question-circle');
    }
    private static function link(Toolbar $toolbar, string $name, string $label, string $view, string $icon = 'chevron-right', array $params = []): void
    {
        $toolbar->linkButton('nef-' . $name, $label)->buttonClass('btn btn-primary')->url(Route::_('index.php?' . http_build_query(['option' => 'com_nicode_easy_forms', 'view' => $view] + $params), false))->icon('icon-' . $icon);
    }
    private static function command(Toolbar $toolbar, string $command, string $label, string $icon, string $class = 'btn btn-primary'): void
    {
        self::action($toolbar, $command, 'COM_NICODE_EASY_FORMS_' . $label, $icon, ['data-nef-command' => $command], $class);
    }
    private static function action(Toolbar $toolbar, string $name, string $label, string $icon, array $attributes, string $class = 'btn btn-primary'): void
    {
        $toolbar->basicButton('nef-' . $name, $label)->icon('icon-' . $icon)->buttonClass($class)->attributes(['data-nef-toolbar' => '', 'type' => 'button'] + $attributes);
    }
}
