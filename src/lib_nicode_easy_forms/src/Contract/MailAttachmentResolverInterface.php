<?php
declare(strict_types=1);
namespace Nicode\EasyForms\Contract;

use Nicode\EasyForms\Actions\{ActionContext, MailAttachment};

interface MailAttachmentResolverInterface
{
    /** @return list<MailAttachment> Authorized bytes in canonical receipt order. */
    public function resolve(array $selectedFields, ActionContext $context): array;
}
