# ADR-0005 — Canonical contract and compilation

Status: Accepted

## Context

The specifications require immutable snapshots and deterministic compilation, but do not fix a wire format. The complete topic specifications remain authoritative. Regeneration has confirmed that the original aggregate matches those specifications; apparent missing text was tool-output truncation, not a repository defect.

## Decision

FormSpec schema `1.0` uses JSON objects for metadata and policies and ordered arrays for elements, fields, rules and actions. UUIDs identify logical entities. Elements form a flat ordered tree using `parent_uuid`; fields reference their element UUID. Canonical JSON recursively sorts object keys and preserves array order. Decimal values use canonical decimal strings to avoid binary floating point loss. Snapshot hashes use SHA-256. Unknown schema versions fail closed.

Compilation is a pure operation: no persistence, activation or mutable historical state. Application publication will coordinate compilation and transactional activation. Compiler diagnostics are structured codes with JSON paths; errors block snapshots. Provider configuration validation is delegated through explicit contracts.

Regenerate MASTER_SPEC and MANIFEST from individual documents using `php tools/docs.php`; no requirements are removed by rebuilding the aggregate.

## Consequences

Tests can exercise compilation without Joomla. Database authoring remains relational. The snapshot format does not prescribe DOM identifiers or database IDs. Persisted publication and Joomla integration require their own acceptance evidence.
