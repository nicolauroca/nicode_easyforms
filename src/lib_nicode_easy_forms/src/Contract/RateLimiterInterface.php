<?php
declare(strict_types=1);
namespace Nicode\EasyForms\Contract;
use Nicode\EasyForms\Security\RateLimitResult;
interface RateLimiterInterface
{
    public function consume(string $scopeHash, int $limit, int $windowSeconds): RateLimitResult;
}
