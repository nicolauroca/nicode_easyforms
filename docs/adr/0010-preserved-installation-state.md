# ADR-0010 — Reinstalación con permisos conservados

Status: Accepted

## Context

Joomla elimina el asset raíz del componente, sus hijos, los parámetros y su
registro de schema al desinstalar. Conservar únicamente las tablas de respuestas
dejaría referencias ACL huérfanas y perdería políticas de almacenamiento.
Además, el package desinstala primero la library, por lo que el script del
componente no puede depender de sus clases durante la eliminación.

## Decision

El script nativo implementa `InstallerScriptInterface` y utiliza solo APIs de
Joomla. Antes de desinstalar en modo conservación, guarda parámetros, versión de
schema y reglas ACL en `installation_state`, separada de respuestas y logs.
La reinstalación restaura assets por nombre estable y actualiza sus IDs en forms.
Los formularios que ya carecían de asset válido permanecen inaccesibles.
La restauración es idempotente y conserva su snapshot hasta completarse.

## Consequences

- No se reescriben payloads ni versiones históricas.
- Las rutas privadas y reglas se conservan en la base de datos protegida.
- Las operaciones de assets siguen el instalador nativo, fuera de transacciones
  de negocio; no se presupone rollback de cambios del sistema de archivos.
- El modo purge debe tener una autorización y un flujo propios; nunca se infiere
  de una desinstalación normal.
