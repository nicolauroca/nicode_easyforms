<?php
declare(strict_types=1);

namespace Nicode\EasyForms\Application;

use Nicode\EasyForms\Contract\CaptchaAdapterInterface;
use Nicode\EasyForms\Infrastructure\Database\FormRepository;
use Nicode\EasyForms\Registry\FieldTypeRegistry;
use Nicode\EasyForms\Rendering\FormRenderer;
use Nicode\EasyForms\Rendering\RenderContext;
use Nicode\EasyForms\Rules\RuleEngine;
use Nicode\EasyForms\Security\AttemptTokens;
use Nicode\EasyForms\Security\CaptchaPolicy;
use Nicode\EasyForms\Security\PublicAccess;
use Nicode\EasyForms\Submission\RequestContext;

/** Shared component/module entry point; no draft is reachable through this API. */
final readonly class FormDisplay
{
    public function __construct(private FormRepository $forms, private FieldTypeRegistry $types, private RuleEngine $rules, private FormRenderer $renderer, private PublicAccess $access, private AttemptTokens $attempts, private CaptchaAdapterInterface $captcha, private ?\Nicode\EasyForms\Registry\ProviderDependencies $dependencies = null, private ?\Nicode\EasyForms\Contract\LifecycleEventsInterface $events = null) {}

    public function render(int $formId, RequestContext $visitor, string $instance, string $action, string $csrfName, array $messages = [], ?array $submitted = null, array $errors = [], ?string $attempt = null, ?array $declarations = null, bool $reset = false): array
    {
        $form = $this->forms->get($formId);
        $this->access->assert($form, $visitor->viewLevels, $visitor->language, time());
        $version = (int) $form['published_version_id'];
        $spec = $this->forms->version($formId, $version); $definition = $spec->toArray();
        $this->dependencies?->assert($spec);
        $spec = \Nicode\EasyForms\Translation\DefinitionTranslations::spec($spec, $visitor->language); $definition = $spec->toArray();
        $messages = \Nicode\EasyForms\Translation\DefinitionTranslations::messages($definition, $messages);
        $eventContext = ['form_uuid' => $definition['uuid'], 'form_id' => $formId, 'version_id' => $version, 'channel' => $visitor->channel, 'locale' => $visitor->language];
        $this->events?->emit('BeforeFormRender', $eventContext);
        if (in_array('repeatable-group', array_column($definition['elements'], 'type'), true) && $declarations === null) {
            if ($submitted !== null) { throw new \InvalidArgumentException('Repeated retry requires its row declarations.'); }
            $declarations = \Nicode\EasyForms\Domain\RepeatedInstances::initial($definition['elements'])->declarations();
        }
        $presentation = new \Nicode\EasyForms\Rules\PresentationState($this->types, $this->rules);
        $state = $declarations === null
            ? $presentation->evaluate($spec, $visitor->ruleContext(), $visitor->trustedValues, $submitted, $reset)
            : $presentation->evaluateInstances($spec, $declarations, $visitor->ruleContext(), $visitor->trustedValues, $submitted, reset: $reset);
        $policy = $definition['security']['captcha'] ?? [];
        $captcha = $this->captcha->render(new CaptchaPolicy($policy['mode'] ?? 'inherit', $policy['provider'] ?? null), $instance);
        $binding = $visitor->sessionBinding . ':' . $visitor->channel;
        if ($attempt !== null) {
            // A pending submission must keep its identity when rendered without JS.
            // Never echo an unverified token or silently turn a retry into a new submission.
            $this->attempts->verify($attempt, $formId, $version, $binding, maximumSeconds: $definition['security']['attempt_lifetime'] ?? 7200);
        } else { $attempt = $this->attempts->issue($formId, $version, $binding); }
        $context = new RenderContext($instance, $formId, $version, $action, $csrfName, $attempt, $messages, channel: $visitor->channel);
        $html = $declarations === null
            ? $this->renderer->render($spec, $context, $state, $errors, $captcha)
            : $this->renderer->renderInstances($spec, $declarations, $context, $state, $errors, $captcha);
        $this->events?->emit('AfterFormRender', $eventContext);
        return ['form_id' => $formId, 'version_id' => $version, 'title' => $definition['name'], 'description' => $definition['metadata']['description'] ?? '', 'html' => $html];
    }
}
