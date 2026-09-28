<?php
declare(strict_types=1);
namespace Nicode\EasyForms\Contract;
interface SecretStoreInterface
{
    public function get(string $reference): string;
}
