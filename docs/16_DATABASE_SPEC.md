# 16 — Database Spec


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Objetivos

El modelo físico debe soportar simultáneamente:

- authoring flexible;
- runtime rápido;
- versionado;
- millones de submissions;
- búsquedas estructuradas;
- portabilidad razonable entre DB soportadas por Joomla;
- migraciones versionadas.

No se creará una tabla por formulario ni una columna por campo.

## 2. Tablas conceptuales

### `#__nicode_easyforms_forms`

Cabecera del Form.

Campos conceptuales:

- bigint ID;
- UUID;
- name;
- alias;
- state;
- access;
- language;
- created/created_by;
- modified/modified_by;
- publish_up/down;
- current_draft_revision;
- published_version_id;
- params/config JSON/text;
- asset_id si se usa ACL por item.

Índices:

- UUID unique;
- state;
- alias cuando proceda;
- published_version;
- modified.

### `#__nicode_easyforms_elements`

Árbol de Authoring.

- ID;
- UUID;
- form_id;
- parent_uuid/id;
- element_type;
- order;
- properties;
- state.

### `#__nicode_easyforms_fields`

Propiedades específicas del field.

- element reference;
- machine_name;
- field_type;
- logical data type;
- config;
- persistence flags;
- index/search flags;
- sensitive flag.

Unique:

`form_id + machine_name`.

### `#__nicode_easyforms_field_options`

Opciones locales.

### `#__nicode_easyforms_rules`

Rules.

### `#__nicode_easyforms_rule_conditions`

Árbol/estructura normalizada de conditions o definición estructurada.

### `#__nicode_easyforms_rule_effects`

Effects.

### `#__nicode_easyforms_actions`

Actions y configuración.

### `#__nicode_easyforms_option_sets`
### `#__nicode_easyforms_option_set_versions`
### `#__nicode_easyforms_option_set_items`

Se separará la identidad del recurso de sus revisiones cuando sea necesario para histórico.

### `#__nicode_easyforms_data_sources`

Configuración sin secretos en claro.

### `#__nicode_easyforms_email_templates`
### `#__nicode_easyforms_form_templates`

Recursos reutilizables.

### `#__nicode_easyforms_form_versions`

- ID;
- form_id;
- revision;
- formspec_schema_version;
- spec payload;
- hash;
- published_at;
- published_by;
- publication_comment.

Unique:

`form_id + revision`.

### `#__nicode_easyforms_submissions`

Cabecera de alto volumen.

Campos conceptuales:

- BIGINT ID;
- UUID;
- form_id;
- form_version_id;
- state;
- received_at;
- processed_at;
- user_id nullable;
- channel;
- locale;
- canonical_payload;
- payload_schema_version;
- action_summary/status;
- retention/anonymization flags;
- optional dedupe/attempt reference;
- created technical fields estrictamente necesarios.

Índices mínimos orientativos:

- UUID unique;
- `(form_id, received_at, id)`;
- `(form_id, state, received_at, id)`;
- `(form_version_id, received_at, id)`;
- `(user_id, received_at, id)` si se utiliza;
- `(action_status, received_at, id)` si la consulta operativa lo justifica.

### `#__nicode_easyforms_submission_index`

Proyección tipada SOLO de campos configurados/indexables.

Conceptualmente:

- submission_id;
- form_id;
- form_version_id;
- field_uuid;
- field_machine_name o field identity optimizada;
- value_type;
- value_keyword;
- value_text corto/normalizado;
- value_integer;
- value_decimal;
- value_boolean;
- value_date;
- value_datetime;
- ordinal para multi-value.

No todos los motores requieren exactamente las mismas columnas; el diseño definitivo debe mantener una capa Repository/SearchProvider.

Índices compuestos se diseñarán alrededor de:

`form_id + field identity + typed value + submission_id`.

### `#__nicode_easyforms_submission_files`

- submission;
- field;
- storage provider;
- storage key;
- original filename saneado;
- media type;
- size;
- checksum;
- metadata;
- timestamps.

### `#__nicode_easyforms_action_runs`

- submission_id;
- action_uuid/type;
- attempt;
- state;
- started/finished;
- result code;
- safe diagnostics;
- next retry metadata si procede.

Índices:

- `(submission_id, action_uuid)`;
- `(state, created_at)` para fallos/jobs.

### `#__nicode_easyforms_audit_log`

Eventos administrativos.

### `#__nicode_easyforms_jobs`

Para exportaciones, reindexados, retención, acciones masivas y procesos que no deban vivir en una petición web.

### `#__nicode_easyforms_job_items` (opcional)

Solo si el Job subsystem necesita granularidad.

## 3. Payload canónico + índice

La fuente primaria de verdad de una Submission será un payload canónico asociado a la FormVersion.

El índice de búsqueda es una proyección regenerable.

Esto permite:

- reconstruir/mostrar la respuesta;
- reindexar;
- cambiar estrategia de búsqueda;
- evitar EAV indiscriminado para todo;
- indexar solo campos útiles.

## 4. Por qué no solo JSON

Un JSON completo es útil para preservación, pero no debe ser el único mecanismo de búsqueda si se esperan millones de filas.

No se diseñará el Administrator alrededor de:

`LIKE '%valor%'` sobre millones de payloads.

## 5. Por qué no EAV completo como única verdad

Persistir cada valor de todos los campos en EAV puede producir decenas/cientos de millones de filas y queries complejas.

Se adopta modelo híbrido:

- canonical payload = fidelidad;
- typed searchable projection = consulta;
- optional external search provider = crecimiento.

## 6. Compatibilidad DB

El core no debe depender de una característica exclusiva de MySQL si Joomla declara también MariaDB/PostgreSQL como motores soportados y decidimos declarar compatibilidad con ellos.

Optimisations específicas podrán existir detrás de adapter/provider y tests.

## 7. IDs

Para tablas masivas se usarán identificadores numéricos amplios adecuados y UUID para identidad externa/portable.

No se deben exponer IDs secuenciales cuando ello permita IDOR.

## 8. Fechas

No usar valores de fecha cero.

Guardar timestamps en representación consistente definida por la arquitectura Joomla/DB.

## 9. Migraciones

Cada cambio de schema:

- script versionado;
- idempotencia razonable;
- forward migration;
- estrategia de datos;
- índice creado de forma segura;
- pruebas sobre datasets representativos.
