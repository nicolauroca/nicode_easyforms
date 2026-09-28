# ADR 0014 — Conservar el límite transaccional ante errores PDO

- Status: Accepted
- Date: 2026-09-27

## Context

La prueba de ocho procesos del limiter descubrió admisiones excesivas con la
creación mediante SELECT/INSERT y una reconexión inesperada en PostgreSQL. Una
prueba aislada de clave duplicada confirmó que el driver PDO de Joomla 6.0.0 puede
interpretar una transacción abortada como pérdida de conexión y repetir SQL fuera
de ella. Una segunda prueba, sin escrituras, demostró que un rollback fallido deja
una profundidad interna con la que el siguiente callback puede correr sin una
transacción real. No es aceptable continuar ni repetir automáticamente escrituras
con un límite transaccional incierto.

## Decision

El limiter crea la ventana mediante INSERT con resolución atómica del conflicto
de unicidad; después bloquea y actualiza el contador. No usa errores de unicidad
como parte del flujo normal de admisión.

Connection conserva DatabaseInterface, QueryInterface, parámetros, fetching y
transacciones Joomla. Sólo al preparar una consulta propia mediante PDO usa una
subclase de PDOStatement que traduce PDOException a ExecutionFailureException.
Eso evita la reconexión/repetición interna y permite que el repositorio revierta
la conexión original. La configuración de clase de statement se restaura en un
finally inmediatamente después de preparar; no cambia consultas ajenas. mysqli
mantiene su ruta nativa. La excepción pública no incorpora SQL ni valores.

Si rollback falla, esa instancia de Connection queda inutilizable. Consultas y
callbacks posteriores fallan antes de ejecutarse. Se conserva la causa original
como excepción anterior; no se intenta reparar a ciegas la profundidad del driver
ni se reintenta una operación parcialmente confirmada. Una nueva petición obtiene
su contexto normal de conexión.

## Verification

La suite de tres motores verifica error de unicidad dentro de savepoint,
rollback de sus escrituras, commit exterior, identidad de conexión y restauración
de la clase de statement. Otra conexión simula pérdida de transacción y comprueba
que no se acepta ninguna consulta o callback posterior. Tres rondas de ocho
procesos por motor obtienen exactamente tres admisiones con límite tres.

## Consequences

Los errores de conexión/SQL no se convierten en replays implícitos de una sentencia.
La recuperación procede por el protocolo idempotente del caso de uso y una nueva
petición o lease. El adaptador requiere comprobar este comportamiento al ampliar
la matriz de versiones de Joomla/PHP. Los probes están limitados a bases de prueba
y no modifican código de Joomla ni la instalación del usuario.
