<?php
declare(strict_types=1);

namespace Nicode\EasyForms\Rendering;

use Nicode\EasyForms\Domain\FormSpec;
use Nicode\EasyForms\Rules\RuleResult;

/** Used identically by component, module and administrative preview. */
final readonly class FormRenderer
{
    public function __construct(private FieldRendererRegistry $fields, private PublicSpec $publicSpec, private ?\Closure $safeHtml = null) {}

    public function render(FormSpec $spec, RenderContext $context, RuleResult $state, array $errors = [], string $captchaHtml = ''): string
    {
        return $this->renderForm($spec, $context, $state, $errors, $captchaHtml);
    }

    public function renderInstances(FormSpec $spec, array $declarations, RenderContext $context, RuleResult $state, array $errors = [], string $captchaHtml = '', int $budget = 10000): string
    {
        $instances = new \Nicode\EasyForms\Domain\RepeatedInstances($spec->toArray()['elements'], $declarations, $budget);
        foreach ($instances->elementAddresses($budget) as $address) {
            if (!isset($state->states[$address->key()])) { throw new \InvalidArgumentException('Missing instance render state.'); }
        }
        return $this->renderForm($spec, $context, $state, $errors, $captchaHtml, $instances, $budget);
    }

    private function renderForm(FormSpec $spec, RenderContext $context, RuleResult $state, array $errors, string $captchaHtml, ?\Nicode\EasyForms\Domain\RepeatedInstances $instances = null, int $budget = 10000): string
    {
        $definition = $spec->toArray();
        $elements = array_column($definition['elements'], null, 'uuid');
        $children = []; $fields = array_column($definition['fields'], null, 'uuid');
        foreach ($definition['elements'] as $element) { $children[$element['parent_uuid'] ?? 'root'][] = $element; }
        $html = '<form' . Html::attributes(['id' => $context->instance, 'class' => 'nef-form', 'method' => 'post', 'action' => $context->action, 'enctype' => 'multipart/form-data', 'data-nef-form' => '', 'data-nef-unavailable' => $context->message('form_unavailable', 'This form is temporarily unavailable.'), 'data-nef-preview' => $context->preview ? 'true' : null]) . '>';
        $html .= '<div class="nef-result" role="status" tabindex="-1" aria-live="polite"></div>';
        if (($definition['metadata']['description'] ?? '') !== '') { $html .= '<p class="nef-description">' . Html::escape($definition['metadata']['description']) . '</p>'; }
        $html .= '<div class="nef-honeypot" aria-hidden="true"><label>' . Html::escape($context->message('leave_empty', 'Leave this empty')) . '<input type="text" name="nef_contact" tabindex="-1" autocomplete="off"></label></div>';
        $html .= '<div class="nef-validation-summary" role="alert" tabindex="-1"' . ($errors === [] ? ' hidden' : '') . '>';
        if ($errors !== []) {
            $html .= '<p>' . Html::escape($context->message('validation_error', 'Please review the highlighted fields.')) . '</p><ul>';
            foreach ($errors as $uuid => $messages) {
                $definitionId = $instances === null ? $uuid : \Nicode\EasyForms\Domain\FieldAddress::fromKey($uuid)->field;
                $html .= '<li><a' . Html::attributes(['href'=>'#' . $context->instance . '-' . $uuid . '-field', 'data-nef-error-target'=>$uuid]) . '>' . Html::escape($fields[$definitionId]['config']['label'] ?? $fields[$definitionId]['name'] ?? $elements[$definitionId]['title'] ?? '') . ': ' . Html::escape(implode(' ', $messages)) . '</a></li>';
            }
            $html .= '</ul>';
        }
        $html .= '</div><div class="nef-elements">';
        $captchaPlaced = false;
        $render = function (string $parent, int $depth = 0, array $scope = []) use (&$render, &$captchaPlaced, $children, $fields, $context, $state, $errors, $captchaHtml, $instances): string {
            if ($depth > \Nicode\EasyForms\Domain\LayoutLimits::MAX_CONTAINER_DEPTH) { throw new \DomainException('Layout exceeds safe rendering depth.'); }
            $html = '';
            foreach ($children[$parent] ?? [] as $element) {
                $definitionId = $element['uuid']; $type = $element['type'];
                $uuid = $instances === null ? $definitionId : (new \Nicode\EasyForms\Domain\FieldAddress($definitionId, $scope))->key();
                $attributes = ['class' => 'nef-element nef-' . $type, 'data-nef-element' => $uuid, 'hidden' => !($state->states[$uuid]['active'] ?? true)];
                foreach ($element['width'] ?? [] as $breakpoint => $width) { $attributes['class'] .= ' nef-' . $breakpoint . '-' . $width; }
                if ($type === 'step') {
                    $attributes['data-nef-step'] = $uuid; $attributes['aria-label'] = $element['title'] ?? '';
                    if (($element['description'] ?? '') !== '') { $attributes['aria-describedby'] = $context->instance . '-' . $uuid . '-description'; }
                }
                if ($type === 'field') {
                    $attributes['id'] = $context->instance . '-' . $uuid . '-field'; $attributes['tabindex'] = '-1';
                    $field = $fields[$definitionId]; $field['uuid'] = $uuid;
                    $classes = $field['config']['css_class'] ?? '';
                    if ($classes !== '' && \Nicode\EasyForms\Field\CommonConfiguration::validClasses($classes)) { $attributes['class'] .= ' ' . $classes; }
                    $field['config']['required'] = $state->states[$uuid]['required'];
                    $field['config']['disabled'] = !$state->states[$uuid]['active'];
                    $field['options'] = $state->states[$uuid]['options'];
                    $html .= '<div' . Html::attributes($attributes) . '>' . $this->fields->get($field['type'])->render($field, $context->instance, $state->states[$uuid]['value'], $errors[$uuid] ?? []) . '</div>';
                } elseif ($type === 'repeatable-group' && $instances !== null) {
                    $attributes['id'] = $context->instance . '-' . $uuid . '-field'; $attributes['tabindex'] = '-1';
                    $attributes['data-nef-repeat-group'] = $uuid;
                    $attributes['aria-describedby'] = $context->instance . '-' . $uuid . '-error';
                    $html .= '<fieldset' . Html::attributes($attributes) . '><legend>' . Html::escape($element['title'] ?? $context->message('repeat_group', 'Entries')) . '</legend>';
                    foreach ($instances->declarations()[$uuid] as $position => $row) {
                        $label = ($element['title'] ?? $context->message('repeat_row', 'Entry')) . ' ' . ($position + 1);
                        $html .= '<fieldset' . Html::attributes(['class'=>'nef-repeat-row','data-nef-repeat-row'=>$row,'data-nef-repeat-scope'=>$uuid]) . '><legend>' . Html::escape($label) . '</legend>';
                        $html .= $render($definitionId, $depth + 1, [...$scope, ['group'=>$definitionId,'instance'=>$row]]);
                        $html .= '<button' . Html::attributes(['type'=>$context->preview ? 'button' : 'submit','name'=>'row_change','value'=>json_encode(['operation'=>'remove','group'=>$uuid,'row'=>$row], JSON_THROW_ON_ERROR),'data-nef-row-change'=>'remove','formnovalidate'=>true,'disabled'=>($context->preview && !$context->previewRows) || count($instances->declarations()[$uuid]) <= $element['repeat']['min'],'aria-label'=>$context->message('remove_row', 'Remove entry') . ': ' . $label]) . '>' . Html::escape($context->message('remove_row', 'Remove entry')) . '</button></fieldset>';
                    }
                    $html .= '<button' . Html::attributes(['type'=>$context->preview ? 'button' : 'submit','name'=>'row_change','value'=>json_encode(['operation'=>'add','group'=>$uuid], JSON_THROW_ON_ERROR),'data-nef-row-change'=>'add','formnovalidate'=>true,'disabled'=>($context->preview && !$context->previewRows) || count($instances->declarations()[$uuid]) >= $element['repeat']['max'],'aria-label'=>$context->message('add_row', 'Add entry') . ': ' . ($element['title'] ?? $context->message('repeat_group', 'Entries'))]) . '>' . Html::escape($context->message('add_row', 'Add entry')) . '</button>';
                    $html .= '<div' . Html::attributes(['id'=>$attributes['aria-describedby'],'class'=>'nef-error','data-nef-error'=>$uuid,'hidden'=>!isset($errors[$uuid])]) . '>' . Html::escape(implode(' ', $errors[$uuid] ?? [])) . '</div></fieldset>';
                } elseif ($type === 'captcha') {
                    if ($captchaPlaced) { throw new \DomainException('Duplicate CAPTCHA placement.'); }
                    $captchaPlaced = true; $html .= '<div' . Html::attributes($attributes) . '>' . $captchaHtml . '</div>';
                } elseif (in_array($type, ['heading', 'subheading', 'paragraph', 'notice', 'safe-html', 'separator', 'spacer'], true)) {
                    $tag = match ($type) { 'heading' => 'h2', 'subheading' => 'h3', 'paragraph' => 'p', default => 'div' };
                    $body = $type === 'safe-html' ? ($this->safeHtml !== null ? ($this->safeHtml)($element['text'] ?? '') : Html::escape($element['text'] ?? '')) : Html::escape($element['text'] ?? '');
                    if ($type === 'separator') { $body = '<hr>'; }
                    $html .= '<' . $tag . Html::attributes($attributes) . '>' . $body . '</' . $tag . '>';
                } else {
                    $tag = $type === 'fieldset' ? 'fieldset' : 'section';
                    $heading = ($element['title'] ?? '') !== '' ? ($tag === 'fieldset' ? '<legend>' : '<h3>') . Html::escape($element['title']) . ($tag === 'fieldset' ? '</legend>' : '</h3>') : '';
                    $description = $type === 'step' && ($element['description'] ?? '') !== '' ? '<p' . Html::attributes(['id' => $attributes['aria-describedby'], 'class' => 'nef-step-description']) . '>' . Html::escape($element['description']) . '</p>' : '';
                    $html .= '<' . $tag . Html::attributes($attributes) . '>' . $heading . $description . $render($definitionId, $depth + 1, $scope) . '</' . $tag . '>';
                }
            }
            return $html;
        };
        $html .= $render('root') . '</div>';
        if (!$captchaPlaced) { $html .= $captchaHtml; }
        $html .= '<div class="nef-navigation"><span data-nef-progress aria-live="polite"></span><button type="button" data-nef-previous hidden>' . Html::escape($context->message('previous', 'Previous')) . '</button><button type="button" data-nef-next hidden>' . Html::escape($context->message('next', 'Next')) . '</button><button type="submit" data-nef-submit' . ($context->preview ? ' disabled' : '') . '>' . Html::escape($context->message('submit', 'Submit')) . '</button></div>';
        foreach (['form_id' => $context->formId, 'version_id' => $context->versionId, 'attempt' => $context->attempt, 'instance' => $context->instance, 'channel' => $context->channel, $context->csrfName => 1] as $name => $value) { $html .= '<input' . Html::attributes(['type' => 'hidden', 'name' => $name, 'value' => $value]) . '>'; }
        $public = $instances === null ? $this->publicSpec->project($spec, $state) : $this->publicSpec->projectInstances($spec, $instances->declarations(), $state, $budget);
        if ($instances !== null) { $html .= '<input' . Html::attributes(['type'=>'hidden','name'=>'nef_instances','value'=>json_encode($instances->declarations(), JSON_THROW_ON_ERROR)]) . '>'; }
        $public['messages'] = $context->messages;
        $json = json_encode($public, JSON_THROW_ON_ERROR | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        return $html . '<script type="application/json" data-nef-definition>' . $json . '</script></form>';
    }
}
