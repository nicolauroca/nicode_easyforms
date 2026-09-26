# 27 — Operaciones, observabilidad y jobs


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Technical Log

Niveles:

- ERROR;
- WARNING;
- INFO;
- DEBUG configurable.

Eventos:

- compiler failure;
- Data Source failure;
- Action failure;
- email failure;
- webhook failure;
- storage failure;
- indexing failure;
- unexpected exception.

## 2. Audit Log

Separado del technical log.

Eventos:

- create/update/publish/unpublish/delete Form;
- restore version;
- change permissions;
- view/reveal sensitive si se decide auditar;
- export;
- anonymize;
- delete Submission;
- retry Action;
- config security change.

## 3. Correlation

Identificadores correlacionables:

- request/correlation ID;
- form UUID;
- version ID;
- submission UUID;
- action run ID;
- job ID.

No incluir PII innecesaria.

## 4. Jobs

Procesos potencialmente grandes:

- export;
- reindex;
- retention;
- anonymization batch;
- delete batch;
- retry batch;
- cleanup;
- post-upgrade migration.

Job:

- ID;
- type;
- creator;
- state;
- parameters snapshot;
- cursor/progress;
- counts;
- error;
- created/started/finished;
- cancellation semantics.

## 5. Ejecución

La implementación podrá apoyarse en mecanismos Joomla apropiados como CLI/plugins/tareas programadas cuando proceda.

No se dependerá de que el usuario mantenga una pestaña abierta para operaciones masivas.

## 6. Estados de Job

- pending;
- running;
- paused/retryable si aplica;
- completed;
- failed;
- cancelled.

## 7. Idempotencia

Jobs resumibles deben evitar reprocesar destructivamente el mismo bloque.

## 8. Diagnóstico

Information System:

- package versions;
- Joomla;
- PHP;
- DB;
- schema;
- FormSpec versions;
- mail;
- filesystem/storage;
- cache;
- CAPTCHA providers;
- search provider;
- pending jobs;
- index lag;
- cron/task capability;
- last maintenance;
- health warnings.

## 9. Maintenance

Tareas:

- expired export cleanup;
- temp upload cleanup;
- retention;
- orphan checks;
- index reconciliation;
- ActionRun cleanup según retention;
- audit/log retention.

## 10. Métricas

Sin imponer una solución externa, exponer datos para:

- submissions/day;
- errors/day;
- failed actions;
- avg processing time futuro;
- index backlog;
- job backlog;
- storage consumption.

## 11. Alertas Administrator

Dashboard muestra problemas accionables:

- retries agotados;
- provider missing;
- jobs stalled;
- storage low/unwritable;
- Form invalid;
- index backlog;
- retention failure.
