<?php
declare(strict_types=1);

namespace Nicode\EasyForms\Contract;

use Nicode\EasyForms\Validation\Violation;

interface ValidatorInterface extends ProviderInterface
{
    /** Only active, normalized values are supplied. @return list<Violation> */
    public function validate(array $values, array $configuration, array $datatypes): array;
}
