<?php
declare(strict_types=1);

function nativeInput(array $post, array $files = [], string $method = 'POST'): Joomla\Input\Input
{
    return new class($post, $files, $method) extends Joomla\Input\Input {
        public function __construct(array $post, array $files, string $method)
        {
            parent::__construct([]);
            $this->inputs['post'] = new Joomla\Input\Input($post);
            $this->inputs['files'] = new Joomla\Input\Files($files);
            $this->inputs['server'] = new Joomla\Input\Input(['REQUEST_METHOD' => $method]);
        }
    };
}

test('packed request values preserve raw types and reject ambiguous or malformed envelopes', function (): void {
    $draft = definition(); $uuid = $draft['fields'][0]['uuid']; $spec = new Nicode\EasyForms\Domain\FormSpec($draft);
    $post = ['form_id' => '1', 'version_id' => '2', 'attempt' => 'signed-token', 'nef_values' => json_encode([$uuid => [' a ', '😀'], 'unknown' => 'ignored'], JSON_THROW_ON_ERROR)];
    same([$uuid => [' a ', '😀']], Nicode\EasyForms\Infrastructure\Joomla\RequestAdapter::request(nativeInput($post), $spec)->values);
    foreach (['[]', 'null', '1', '"text"', '{', "{\"bad\":\"\xFF\"}", str_repeat(' ', 2097153)] as $invalid) {
        raises(InvalidArgumentException::class, fn () => Nicode\EasyForms\Infrastructure\Joomla\RequestAdapter::request(nativeInput(array_replace($post, ['nef_values' => $invalid])), $spec));
    }
    raises(InvalidArgumentException::class, fn () => Nicode\EasyForms\Infrastructure\Joomla\RequestAdapter::request(nativeInput($post + ['nef' => [$uuid => 'mixed']]), $spec));
    same([], Nicode\EasyForms\Infrastructure\Joomla\RequestAdapter::request(nativeInput(array_replace($post, ['nef_values' => '{}'])), $spec)->values);
});

test('POST byte limits distinguish definite overflow from session failures without integer overflow', function (): void {
    $input = nativeInput([]);
    foreach ([['16384', '16K', false], ['16385', '16K', true], ['00016385', '16K', true], ['999999999999999999999999', '8M', true], ['1', '0', false], ['999', '-1', false], ['12junk', '1', false], [null, '1', false], [[], '1', false], ['1025', '1k', true], ['1', 'invalid', false]] as [$length, $limit, $expected]) {
        $input->server->set('CONTENT_LENGTH', $length);
        same($expected, Nicode\EasyForms\Infrastructure\Joomla\RequestAdapter::exceedsPostSize($input, $limit));
    }
    $get = nativeInput([], method: 'GET'); $get->server->set('CONTENT_LENGTH', '999');
    same(false, Nicode\EasyForms\Infrastructure\Joomla\RequestAdapter::exceedsPostSize($get, '1'));
});

test('Joomla request adapter preserves raw field values while rejecting malformed identities and envelopes', function (): void {
    $draft = definition(); $uuid = $draft['fields'][0]['uuid']; $spec = new Nicode\EasyForms\Domain\FormSpec($draft);
    $post = ['form_id' => '1', 'version_id' => '2', 'attempt' => 'signed-token', 'nef' => [$uuid => ' <literal> ', 'forged' => 'ignored']];
    $request = Nicode\EasyForms\Infrastructure\Joomla\RequestAdapter::request(nativeInput($post), $spec);
    same(1, $request->formId); same(2, $request->versionId); same([$uuid => ' <literal> '], $request->values);
    foreach ([['form_id' => '1 OR 1=1'], ['form_id' => ['1']], ['version_id' => '9223372036854775808'], ['attempt' => ['x']], ['nef' => 'unexpected'], ['easyforms_captcha' => ['forged']], ['nef_contact' => ['forged']]] as $invalid) {
        raises(InvalidArgumentException::class, fn () => Nicode\EasyForms\Infrastructure\Joomla\RequestAdapter::request(nativeInput(array_replace($post, $invalid)), $spec));
    }
    raises(InvalidArgumentException::class, fn () => Nicode\EasyForms\Infrastructure\Joomla\RequestAdapter::request(nativeInput($post, method: 'GET'), $spec));
});

test('Joomla request adapter decodes native multipart shape and leaves provenance to the upload gateway', function (): void {
    $draft = definition(); $uuid = $draft['fields'][0]['uuid']; $draft['fields'][0]['type'] = 'multiple-files';
    $spec = new Nicode\EasyForms\Domain\FormSpec($draft);
    $files = ['nef' => ['name' => [$uuid => ['one.txt', '']], 'type' => [$uuid => ['browser/mime', '']], 'tmp_name' => [$uuid => ['/untrusted/path', '']], 'error' => [$uuid => [UPLOAD_ERR_OK, UPLOAD_ERR_NO_FILE]], 'size' => [$uuid => [999, 0]]]];
    $post = ['form_id' => '1', 'version_id' => '2', 'attempt' => 'signed-token', 'nef' => [$uuid => ['tmp_name' => '/forged/value']]];
    $request = Nicode\EasyForms\Infrastructure\Joomla\RequestAdapter::request(nativeInput($post, $files), $spec);
    same([['name' => 'one.txt', 'tmp_name' => '/untrusted/path', 'error' => UPLOAD_ERR_OK]], $request->files[$uuid]);
    same(false, isset($request->files[$uuid][0]['type'])); same(false, isset($request->files[$uuid][0]['size']));
    $packedPost = $post; unset($packedPost['nef']); $packedPost['nef_values'] = '{}';
    same($request->files, Nicode\EasyForms\Infrastructure\Joomla\RequestAdapter::request(nativeInput($packedPost, $files), $spec)->files);
    $draft['fields'][0]['type'] = 'text';
    same([], Nicode\EasyForms\Infrastructure\Joomla\RequestAdapter::request(nativeInput($post, $files), new Nicode\EasyForms\Domain\FormSpec($draft))->files);
});
