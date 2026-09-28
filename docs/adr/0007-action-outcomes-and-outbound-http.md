# ADR-0007 — Action outcomes and outbound HTTP

Status: accepted for implementation; endpoint and real transport acceptance pending.

Action claims serialize briefly on the submission row, then release the database
transaction before invoking a provider. Execution order is numeric order followed
by UUID. A blocking failure retains the received submission and stops later actions;
it cannot roll back an email or other external side effect.

Successful and skipped runs are not repeated. A known failure may be retried
explicitly. Timeouts, interrupted running workers and unknown provider outcomes
remain uncertain and are not automatically repeated. Database completion failure
after a side effect propagates without a second attempt to finish the same lease.
Action contexts must match the submission UUID and immutable version hash.

Webhooks use POST/PUT/PATCH over HTTPS on port 443 to exact administrator-approved
DNS names. All resolved addresses must pass the public-address policy; cURL pins
the chosen address while retaining TLS hostname verification. Redirects, proxies,
URL credentials and visitor-selected destinations are forbidden. Requests and
responses are bounded and deadlines are enforced. Secret references resolve only
on the server. Optional signatures use HMAC-SHA256 over timestamp plus canonical
JSON body; idempotency headers bind submission and action identity.

The initial public-address policy intentionally rejects special-purpose IPv4,
non-global IPv6, mapped IPv6, transition and documentation ranges. Sites needing
private integration endpoints require a separately reviewed transport provider,
not a per-form bypass of this policy.

Post-submit messages distinguish recorded responses from completed processing.
Messages are plain text with a restricted token set. Sensitive values and files
are excluded from browser value-preservation defaults. Navigation is resolved
from configured menu items, internal routes or explicitly approved HTTPS hosts.
