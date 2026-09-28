<?php
declare(strict_types=1);

namespace Nicode\EasyForms\Contract;

use Nicode\EasyForms\Domain\FormSpec;
use Nicode\EasyForms\Search\SearchPage;
use Nicode\EasyForms\Search\SearchRequest;
use Nicode\EasyForms\Search\SearchScope;

interface SearchProviderInterface extends ProviderInterface
{
    public function search(SearchRequest $request, SearchScope $scope, ?FormSpec $selectedForm = null): SearchPage;
}
