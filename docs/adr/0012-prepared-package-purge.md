# ADR-0012 — Prepare package purge before uninstalling code

Status: Accepted

## Decision

Preserve is the default native uninstall. Purge is an explicit administrative
workflow: review aggregate form/response/file counts and approximate owned bytes,
confirm permanent removal, prepare cleanup through jobs, then use Joomla's native
package uninstaller. Only component administrators may request or resume it.

Confirmation stores a durable purge marker in installation state and immediately
blocks public access and ordinary administrator writes. New form creation and
the marker transition serialize on the schema-state row. Existing in-flight
submissions serialize with each form's deletion transition. The orchestrator
queues bounded groups of form deletion jobs, revokes export jobs and waits for
all owned-file/export outboxes to finish. Missing or failed providers prevent
readiness, retain object keys and expose a retryable operational state.

The ready marker permits schema removal only after native preflight independently
rechecks that forms/submissions/files are absent and cleanup is complete. This
check runs at package level before any child is removed and again in the component
script. Installer cleanup is self-contained and never depends on library removal
order. It drops only the statically enumerated extension tables, using the selected
dialect, and clears only extension cache groups. External directories and objects
without recorded ownership are never recursively removed.

Preparation is irreversible. It cannot be cancelled into a functioning partially
purged installation. An authorized administrator may resume failed preparation;
workers revalidate permissions. A normal uninstall cannot discard active cleanup
queues. Reinstallation after a completed purge starts with empty extension data.

## Consequences

Large response deletion remains outside installer requests. Filesystem and database
work are not a distributed transaction: durable cleanup records must survive until
physical deletion succeeds. Staged upload cleanup failures must also enter this
outbox. Native tests must cover blocked early uninstall, successful preparation,
physical cleanup, schema removal and clean reinstall on each supported engine.
