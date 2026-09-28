<?php
declare(strict_types=1);

namespace Nicode\EasyForms\Contract;

use Nicode\EasyForms\Actions\MailMessage;

interface MailTransportInterface
{
    public function send(MailMessage $message): void;
}
