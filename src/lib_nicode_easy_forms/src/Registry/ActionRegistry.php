<?php
declare(strict_types=1);

namespace Nicode\EasyForms\Registry;

use Nicode\EasyForms\Contract\ActionInterface;

/** @extends ProviderRegistry<ActionInterface> */
final class ActionRegistry extends ProviderRegistry
{
    public function __construct() { parent::__construct(ActionInterface::class); }
}
