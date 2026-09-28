<?php
declare(strict_types=1);
// This shared site is never purged. Positive destructive acceptance lives in
// tests/joomla-purge.php on separately guarded lifecycle databases.
$purgeReview = $api('purge.review');
$assert($purgeReview['state'] === 'preserve' && $purgeReview['forms'] > 0 && strlen($purgeReview['confirmation']) === 64, 'Native purge review failed.');
$purgePage = $request($base . '?option=com_nicode_easy_forms&view=purge');
$assert($purgePage['status'] === 200 && str_contains($purgePage['body'], 'data-nef-purge-confirm') && str_contains($purgePage['body'], 'DELETE EASYFORMS DATA') && str_contains(strtolower($purgePage['headers']), 'no-store'), 'Native purge confirmation view failed.');
$api('purge.prepare', expected: 405);
$purgeMissingCsrf = $request($base . '?option=com_nicode_easy_forms&task=purge.prepare', ['confirmation' => $purgeReview['confirmation'], 'phrase' => 'DELETE EASYFORMS DATA']);
$assert($purgeMissingCsrf['status'] === 403, 'Purge accepted missing CSRF.');
$purgeWrongPhrase = $request($base . '?option=com_nicode_easy_forms&task=purge.prepare', ['confirmation' => $purgeReview['confirmation'], 'phrase' => 'wrong', $token => '1']);
$assert($purgeWrongPhrase['status'] === 409 && $api('purge.review')['state'] === 'preserve', 'Unconfirmed purge changed shared fixture data.');
echo "Native purge HTTP: protected aggregate review, no-store view, POST/CSRF and exact confirmation phrase passed without purging the shared site.\n";
