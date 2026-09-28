<?php
declare(strict_types=1);

namespace Nicode\EasyForms\Registry;

use Nicode\EasyForms\Contract\FieldTypeInterface;

/** @extends ProviderRegistry<FieldTypeInterface> */
final class FieldTypeRegistry extends ProviderRegistry
{
    public function __construct()
    {
        parent::__construct(FieldTypeInterface::class);
    }
}
