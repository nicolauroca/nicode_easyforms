<?php
declare(strict_types=1);

namespace Nicode\EasyForms\Registry;

use Nicode\EasyForms\Contract\ValidatorInterface;
use Nicode\EasyForms\Validation\RelationalValidator;

/** @extends ProviderRegistry<ValidatorInterface> */
final class ValidatorRegistry extends ProviderRegistry
{
    public function __construct() { parent::__construct(ValidatorInterface::class); }
    public static function core(): self
    {
        $registry = new self();
        foreach (RelationalValidator::IDS as $id) { $registry->register(new RelationalValidator($id)); }
        return $registry;
    }
}
