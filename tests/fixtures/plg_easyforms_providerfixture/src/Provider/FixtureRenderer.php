<?php
declare(strict_types=1);
namespace NicodeFixture\Plugin\Easyforms\ProviderFixture\Provider;

final readonly class FixtureRenderer implements \Nicode\EasyForms\Contract\FieldRendererInterface
{
    public function render(array $field, string $instance, mixed $value, array $errors = []): string
    {
        $field['type'] = 'text';
        return '<div class="fixture-uppercase">' . (new \Nicode\EasyForms\Rendering\CoreFieldRenderer())->render($field, $instance, $value, $errors) . '</div>';
    }
}
