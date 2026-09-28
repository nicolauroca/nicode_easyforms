# ADR 0013 — Registro durable de subidas antes de escribir

- Status: Accepted
- Date: 2026-09-27

## Context

El outbox de limpieza de una respuesta sólo conoce archivos ya asociados a ella.
Un proceso puede morir después de crear bytes privados y antes de guardar la
respuesta. Además, una purga no puede declararse preparada mientras otro proceso
todavía pueda crear un archivo. Rastrear nombres o borrar todo un directorio no
demuestra propiedad y pone en riesgo archivos ajenos.

## Decision

Las subidas HTTP usan un registro `upload_staging`, independiente de las filas de
respuesta. El proveedor reserva una clave opaca nueva sin crear el objeto. Se
guarda y confirma la propiedad (formulario, proveedor, clave, token aleatorio y
caducidad) antes de escribir. El contrato opcional `StagedStorageProviderInterface`
separa reserva de clave y escritura exclusiva; el almacenamiento local lo cumple.
Un proveedor sin ese contrato no puede recibir subidas HTTP gestionadas.

La escritura adquiere un bloqueo compartido de la fila de schema, comprueba la
purga y el formulario, y bloquea su reserva. Mantiene esos bloqueos hasta terminar
de escribir y guardar tamaño/checksum. La reserva inicial ya confirmada sobrevive
al rollback o muerte del proceso. Los bloqueos compartidos permiten subidas
concurrentes; la preparación/finalización de purga usa el bloqueo exclusivo.

La persistencia de la respuesta consume la reserva dentro de la misma transacción
que inserta `submission_files`. Comprueba formulario, token, caducidad, tamaño y
checksum. Las reservas no se recrean ni se reutilizan. Un descarte sólo puede
actuar sobre una reserva que aún pertenece al solicitante; una reserva consumida
no autoriza borrar un archivo ya vinculado, incluso tras un commit ambiguo.

Reservas abandonadas caducan a la hora. Un job acotado las transfiere al outbox de
limpieza sin inspeccionar rutas ni nombres originales. La purga invalida nuevas
reservas y vacía todas las reservas existentes mediante el mismo outbox antes de
marcarse lista. La transferencia reserva→outbox es atómica. La eliminación de un
formulario también transfiere sus reservas. El registro no tiene FK al formulario
para conservar la obligación de limpieza ante interrupciones de eliminación.

## Consequences

La base de datos guarda claves y tokens técnicos privados durante el staging;
no se proyectan al navegador ni al log. Un fallo de almacenamiento o de base de
datos bloquea la subida. Una purga espera a escrituras activas y a limpieza real.
El registro sólo demuestra propiedad de objetos que pasaron por el protocolo;
archivos preexistentes desconocidos permanecen intactos. APIs de almacenamiento
de bajo nivel siguen requiriendo que su llamador gestione propiedad y limpieza.

## Alternatives

Un barrido de directorio no distingue archivos propios de ajenos. Registrar sólo
después de escribir conserva la ventana de pérdida. Una lease temporal sin un
bloqueo que impida crear bytes después de la purga permite que un escritor pausado
reanude demasiado tarde. Mantener un único bloqueo exclusivo para todas las
subidas serializaría innecesariamente escrituras independientes.
