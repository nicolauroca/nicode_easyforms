<?php
declare(strict_types=1);

namespace Nicode\EasyForms\Registry;

use Nicode\EasyForms\Contract\JobHandlerInterface;

/** @extends ProviderRegistry<JobHandlerInterface> */
final class JobHandlerRegistry extends ProviderRegistry
{
    public function __construct() { parent::__construct(JobHandlerInterface::class); }
}
