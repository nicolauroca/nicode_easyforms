<?php
declare(strict_types=1);
namespace Nicode\EasyForms\Security;
final class Permissions
{
    public const ALL = ['core.admin', 'core.options', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete', 'easyforms.forms.manage', 'easyforms.forms.publish', 'easyforms.submissions.view', 'easyforms.submissions.manage', 'easyforms.submissions.export', 'easyforms.submissions.delete', 'easyforms.submissions.anonymize', 'easyforms.submissions.reindex', 'easyforms.submissions.retry', 'easyforms.submissions.view_sensitive', 'easyforms.resources.manage', 'easyforms.logs.view', 'easyforms.jobs.manage'];
}
