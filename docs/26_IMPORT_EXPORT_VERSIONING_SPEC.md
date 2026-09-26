# 26 — Import, export y versionado


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Export de Form

Debe incluir:

- metadata portable;
- layout;
- fields;
- local options;
- references/resources según modo;
- rules;
- validations;
- actions sin secretos;
- presentation;
- post-submit;
- privacy/storage policy;
- translations;
- FormSpec schema version.

No incluir por defecto:

- submissions;
- secrets;
- API keys;
- provider credentials.

## 2. Modos de export

### Portable
Incluye recursos embebibles.

### Reference-aware
Puede conservar references a recursos si destino compatible.

## 3. Import

Pipeline:

- parse;
- schema validation;
- version compatibility;
- migration;
- security validation;
- dependency analysis;
- UUID collision policy;
- preview;
- import as draft.

Nunca publicar automáticamente un Form importado sin validación/decisión explícita.

## 4. UUID

Import en misma instalación deberá decidir:

- duplicate → new UUID;
- update existing → proceso explícito;
- conflict → error/prompt.

## 5. Versiones

Cada Publish genera FormVersion.

No sobrescribir versiones antiguas.

## 6. Compare

Administrator debería poder comparar dos revisiones:

- fields added/removed;
- property changes;
- rules;
- options;
- Actions;
- messages;
- privacy.

## 7. Restore

Restaurar = crear draft nuevo basado en snapshot antiguo.

## 8. Option Sets

Recursos versionados requieren reglas claras:

- snapshot embedded;
- pinned version;
- dependency import.

## 9. Submission export

Separado de Form definition export.

Puede ser:

- filtered dataset;
- fields selection;
- historical label strategy;
- file references;
- metadata selection.

## 10. Reproducibilidad

Un FormSpec exportado debe poder validarse sin depender de la estructura SQL interna.
