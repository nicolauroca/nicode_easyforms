<?php
declare(strict_types=1);

namespace Nicode\EasyForms\Registry;

use Nicode\EasyForms\Compiler\FormCompiler;
use Nicode\EasyForms\Contract\RuleEffectInterface;
use Nicode\EasyForms\Rules\CoreEffect;

/** @extends ProviderRegistry<RuleEffectInterface> */
final class RuleEffectRegistry extends ProviderRegistry
{
    public function __construct() { parent::__construct(RuleEffectInterface::class); }
    public static function core(): self
    {
        $registry = new self();
        foreach (FormCompiler::EFFECTS as $id) { $registry->register(new CoreEffect($id)); }
        return $registry;
    }
}
