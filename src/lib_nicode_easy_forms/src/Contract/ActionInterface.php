<?php
declare(strict_types=1);

namespace Nicode\EasyForms\Contract;

use Nicode\EasyForms\Actions\ActionContext;
use Nicode\EasyForms\Actions\ActionOutcome;

interface ActionInterface extends ProviderInterface
{
    public function execute(array $configuration, ActionContext $context): ActionOutcome;
}
