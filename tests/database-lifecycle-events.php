<?php
declare(strict_types=1);
$eventDispatcher = new Joomla\Event\Dispatcher(); $eventPhases = []; $eventVeto = false; $eventLogs = 0;
foreach (Nicode\EasyForms\Infrastructure\Joomla\LifecycleEvent::PHASES as $phase) {
    $eventDispatcher->addListener('onEasyForms' . $phase, static function (Nicode\EasyForms\Infrastructure\Joomla\LifecycleEvent $event) use (&$eventPhases, &$eventVeto): void {
        $eventPhases[] = $event->phase;
        if ($eventVeto && in_array($event->phase, ['BeforeSubmissionPersist', 'BeforeAction'], true)) { throw new DomainException('Synthetic veto'); }
        if ($event->phase === 'AfterSubmissionPersist') { throw new RuntimeException('Synthetic observer outage'); }
    });
}
$lifecycleEvents = new Nicode\EasyForms\Infrastructure\Joomla\LifecycleEvents($eventDispatcher, static fn () => null, static function () use (&$eventLogs): void { $eventLogs++; });
$eventDraft = $forms->draft($pipelineForm); $eventDraft['persistence'] = ['mode' => 'full'];
$eventRevision = $forms->saveDraft($pipelineForm, (int) $forms->get($pipelineForm)['draft_revision'], $eventDraft, 1); $eventVersion = $forms->publish($pipelineForm, $eventRevision, 1);
$eventPipeline = new Nicode\EasyForms\Application\SubmissionPipeline($forms, $submissions, new Nicode\EasyForms\Security\PublicAccess(), $attemptTokens, $captchaFixture, new Nicode\EasyForms\Infrastructure\Database\RateLimiter($connection), $validationEngine, $pipelineActions, $postSubmit, $storageProviders, events: $lifecycleEvents);
$eventRequest = new Nicode\EasyForms\Submission\SubmitRequest($pipelineForm, $eventVersion, $attemptTokens->issue($pipelineForm, $eventVersion, 'session-fixture:component'), [$pipelineField => 'Lifecycle private answer']);
$eventResult = $eventPipeline->submit($eventRequest, $requestContext);
if (!$eventResult['accepted'] || $eventPhases !== ['BeforeValidation', 'AfterValidation', 'BeforeSubmissionPersist', 'AfterSubmissionPersist'] || $eventLogs !== 1) { throw new RuntimeException('Lifecycle order or observer failure isolation failed.'); }
$eventPhases = []; $eventReplay = $eventPipeline->submit($eventRequest, $requestContext);
if (!($eventReplay['replayed'] ?? false) || $eventPhases !== ['BeforeValidation', 'AfterValidation']) { throw new RuntimeException('Replay repeated persistence events.'); }
$eventVeto = true; $eventPhases = [];
$eventCount = (int) $connection->row('SELECT COUNT(*) AS n FROM ' . $connection->table('submissions') . ' WHERE form_id = :id', [':id' => $pipelineForm])['n'];
$vetoRequest = new Nicode\EasyForms\Submission\SubmitRequest($pipelineForm, $eventVersion, $attemptTokens->issue($pipelineForm, $eventVersion, 'session-fixture:component'), [$pipelineField => 'Rejected by extension']);
if ($eventPipeline->submit($vetoRequest, $requestContext)['accepted'] || (int) $connection->row('SELECT COUNT(*) AS n FROM ' . $connection->table('submissions') . ' WHERE form_id = :id', [':id' => $pipelineForm])['n'] !== $eventCount) { throw new RuntimeException('Before-persist veto wrote a response.'); }
$eventActionSubmission = $submissions->persist($id, $actionVersion, $actionSpec, [], hash('sha256', random_bytes(32)));
$eventActionContext = new Nicode\EasyForms\Actions\ActionContext($actionSpec, [], $eventActionSubmission->uuid, gmdate(DATE_ATOM));
$eventActions = new Nicode\EasyForms\Actions\ActionEngine($actionRegistry, $runs, $conditionEngine, $registry, events: $lifecycleEvents); $callsBeforeVeto = $actionProvider->calls;
$eventActions->execute($eventActionSubmission->id, $eventActionContext);
if ($actionProvider->calls !== $callsBeforeVeto || $runs->latest($eventActionSubmission->id, $actionIds['success'])['state'] !== 'failed') { throw new RuntimeException('Before-action veto executed a provider or became uncertain.'); }
echo "Native lifecycle dispatcher: validation/persist ordering, observer isolation, replay suppression and definite pre-action veto verified.\n";
