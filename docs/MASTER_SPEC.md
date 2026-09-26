# Nicode EasyForms — MASTER SPEC
> Documento agregado para consulta integral. Los documentos temáticos de `docs/` siguen siendo la fuente normativa por materia.


---

<!-- SOURCE: 00_PRODUCT_VISION.md -->

# 00 — Product Vision


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Producto

**Nombre:** Nicode EasyForms.

Nicode EasyForms será un sistema integral de creación, publicación, procesamiento, consulta y administración de formularios para Joomla 6.

No será una colección de formularios programados. Será un **motor declarativo de formularios**.

> El código implementa capacidades; los datos definen cada formulario.

## 2. Objetivos

El producto DEBE permitir:

- crear una cantidad no limitada artificialmente de formularios;
- editar, duplicar, desactivar, publicar, archivar, enviar a papelera y eliminar formularios;
- construir cada formulario con una cantidad no limitada artificialmente de elementos;
- utilizar campos primitivos, elementos de presentación, contenedores, grupos, secciones y pasos;
- definir validaciones simples y entre campos;
- definir lógica condicional de visibilidad, obligatoriedad, habilitación, valores y opciones;
- definir dependencias entre listas y fuentes de datos;
- publicar cualquier formulario como una página seleccionable desde un elemento de menú Joomla;
- publicar cualquier formulario mediante un único módulo configurable;
- mostrar varias instancias del mismo o de distintos formularios en una misma página;
- almacenar de forma íntegra las submissions cuando la política del formulario así lo indique;
- consultar cómodamente cientos o millones de submissions desde Administrator;
- buscar, filtrar, ordenar, segmentar, inspeccionar y exportar respuestas;
- configurar qué ocurre después de un envío: persistencia, mensajes, emails, autorespuestas, redirecciones, webhooks y futuras Actions;
- mantener histórico de versiones del formulario;
- preservar el significado histórico de las respuestas;
- importar y exportar definiciones;
- funcionar con ACL, idiomas, assets, base de datos, mail y demás servicios Joomla mediante APIs modernas;
- integrar CAPTCHA mediante los mecanismos de Joomla y proveedores instalados.

## 3. Principio SPEC-DRIVEN

Toda capacidad significativa DEBE definirse antes de considerarse implementable:

1. requisito;
2. contrato de datos;
3. estados y transiciones;
4. permisos;
5. validaciones;
6. errores;
7. criterios de aceptación;
8. pruebas.

Los requisitos tendrán identificadores estables como `FORM-001`, `FIELD-001`, `SUB-001`, `RULE-001`, `ACTION-001`, `SEC-001`.

## 4. Principio DATA-DRIVEN

No existirán implementaciones como:

- `soporte.php`;
- `contacto.php`;
- `inscripcion.php`;
- `registro_evento.php`.

Existirá un único runtime capaz de interpretar la definición publicada de cualquier formulario.

También serán datos:

- campos;
- estructura;
- opciones;
- reglas;
- validaciones;
- acciones;
- emails;
- mensajes;
- comportamiento post-submit;
- política de persistencia;
- política de privacidad;
- comportamiento de publicación.

## 5. Declarativo, no ejecutable

DATA-DRIVEN no autoriza a guardar código arbitrario en base de datos.

El producto NO DEBE aceptar como funcionalidad estándar:

- PHP arbitrario;
- `eval`;
- JavaScript arbitrario;
- SQL arbitrario escrito en el Builder;
- rutas de archivo arbitrarias;
- plantillas capaces de ejecutar código.

Las expresiones admitidas deberán pertenecer a un lenguaje declarativo limitado y validable.

## 6. Escala

El diseño DEBE contemplar desde el primer día:

- cientos de formularios;
- formularios con cientos de campos si el caso lo requiere;
- millones de submissions;
- decenas o cientos de millones de valores indexables en escenarios extremos;
- exportaciones que no caben razonablemente en una única petición HTTP;
- búsquedas que no dependan de recorrer el payload completo de cada respuesta.

La escala no implica que la primera release deba incluir un clúster externo de búsqueda. Sí implica que el dominio y las interfaces no pueden impedir añadirlo.

## 7. Experiencia objetivo

Para un administrador funcional, crear un formulario debe parecer una operación de configuración.

Para un desarrollador, ampliar el sistema debe hacerse mediante contratos y registries, sin introducir excepciones por `form_id`.

Para soporte, una submission debe ser trazable desde su recepción hasta cada Action ejecutada.

Para auditoría, debe poder saberse qué versión de formulario y qué textos estaban vigentes en el momento del envío.

## 8. Calidad

La extensión DEBE perseguir:

- seguridad por diseño;
- accesibilidad WCAG 2.2 AA como objetivo;
- compatibilidad con Joomla 6.x declarada y probada;
- independencia razonable del motor de base de datos soportado por Joomla;
- ausencia de dependencia de APIs legacy;
- rendimiento predecible;
- trazabilidad;
- observabilidad;
- capacidad de migración futura.


---

<!-- SOURCE: 01_ARCHITECTURE.md -->

# 01 — Arquitectura


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Unidad de distribución

Nicode EasyForms se distribuirá como un **Joomla Package**:

`pkg_nicode_easy_forms`

Constituyentes iniciales:

### `com_nicode_easy_forms`

Componente principal.

Responsabilidades:

- Administrator;
- Builder;
- formularios y versiones;
- submissions;
- recursos;
- configuración;
- Menu Item de frontend;
- controllers de envío;
- endpoints administrativos;
- reporting operativo;
- import/export.

### `mod_nicode_easy_forms`

Módulo de Site.

Responsabilidad principal:

- seleccionar un formulario;
- aportar parámetros estrictamente de presentación del módulo;
- pedir al runtime compartido que lo renderice.

El módulo NO DEBE duplicar reglas, validación o procesamiento.

### `lib_nicode_easy_forms`

Librería compartida.

Contendrá contratos y servicios reutilizables:

- FormSpec;
- Form Compiler;
- Field Type Registry;
- Layout Registry;
- Validator Registry;
- Rule Engine;
- Data Source Registry;
- Renderer;
- Submission Engine;
- Action Engine;
- Storage abstractions;
- Search abstractions;
- servicios comunes.

## 2. Extensiones futuras

La arquitectura DEBE permitir añadir sin rediseñar el núcleo:

- plugin de contenido para insertar formularios;
- Field Types adicionales;
- Validators adicionales;
- Data Sources adicionales;
- Actions adicionales;
- Storage Providers;
- Search Providers;
- integraciones REST;
- integraciones CRM/ERP;
- comandos CLI;
- tareas programadas.

## 3. Capas

### Domain

Entidades, value objects, políticas y contratos puros.

### Application

Casos de uso:

- crear formulario;
- guardar borrador;
- compilar;
- publicar;
- recibir submission;
- ejecutar Action;
- buscar submissions;
- exportar;
- eliminar/anonimizar.

### Infrastructure

Adaptadores:

- Joomla;
- base de datos;
- mail;
- filesystem;
- CAPTCHA;
- ACL;
- cache;
- HTTP;
- logs.

### Presentation

- Administrator;
- Site component;
- module;
- JSON endpoints.

No se exigirá una interpretación dogmática de Clean Architecture, pero una plantilla PHP NO DEBE contener consultas de negocio ni lógica de dominio.

## 4. Authoring Model y Runtime Model

La arquitectura separará:

### Authoring Model

Modelo editable y relacional:

- forms;
- elements;
- fields;
- rules;
- options;
- actions;
- translations.

### Runtime Model

Snapshot publicado, validado e inmutable:

`FormSpec`

El frontend deberá cargar normalmente una versión compilada, no reconstruir el formulario mediante docenas de queries.

## 5. Flujo de publicación

Borrador editable  
→ validación estructural  
→ resolución de referencias  
→ detección de ciclos  
→ validación de Actions  
→ compilación  
→ snapshot FormSpec  
→ versión publicada inmutable  
→ invalidación de cache correspondiente.

## 6. Flujo de ejecución

Request  
→ Joomla Controller  
→ autorización/contexto  
→ CSRF cuando corresponda  
→ anti-spam/CAPTCHA  
→ carga FormSpec exacto  
→ normalización  
→ Rule Engine servidor  
→ resolución de opciones  
→ validación  
→ procesamiento de archivos  
→ persistencia  
→ Action Engine  
→ Post-submit Response.

## 7. Joomla moderno

La implementación DEBE basarse en:

- MVC de Joomla;
- Dependency Injection;
- DatabaseInterface;
- ACL Joomla;
- Web Asset Manager;
- APIs actuales de formularios cuando sean adecuadas;
- sistema de plugins/eventos;
- servicios de mail;
- cache y logging de Joomla cuando sean adecuados.

No deberá depender de `JFactory` ni de APIs legacy como requisito de funcionamiento.

## 8. Independencia entre canales

Component page, module y futuros canales DEBEN utilizar:

- el mismo FormSpec;
- el mismo Renderer;
- el mismo Rule Engine;
- la misma validación;
- el mismo Submission Engine;
- el mismo Action Engine.

Solo podrá variar el contexto de renderizado.

## 9. Registro de capacidades

Los conceptos extensibles se resolverán mediante registries:

- FieldTypeRegistry;
- ValidatorRegistry;
- RuleOperatorRegistry;
- RuleEffectRegistry;
- DataSourceRegistry;
- ActionRegistry;
- StorageProviderRegistry;
- SearchProviderRegistry.

El core deberá conocer interfaces, no todas las implementaciones futuras.


---

<!-- SOURCE: 02_DOMAIN_MODEL.md -->

# 02 — Modelo de dominio


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Entidades principales

### Form

Raíz funcional de un formulario.

Propiedades conceptuales:

- `id`;
- `uuid`;
- `name`;
- `alias`;
- descripción administrativa;
- título visible;
- descripción visible;
- estado;
- idioma;
- access level;
- owner/created_by;
- fechas;
- versión publicada;
- política de almacenamiento;
- política post-submit;
- política de privacidad;
- política anti-spam;
- metadatos de presentación.

### FormVersion

Snapshot inmutable publicado.

Incluye:

- `id`;
- `form_id`;
- número de revisión;
- `formspec_schema_version`;
- FormSpec completo;
- fecha;
- autor;
- comentario de publicación;
- hash de integridad;
- estado histórico.

### Element

Nodo del árbol estructural.

Puede ser:

- field;
- container;
- presentation element;
- step.

### Field

Elemento que representa un valor o capacidad de entrada.

Tiene:

- UUID estable;
- machine name;
- Field Type;
- propiedades;
- validaciones;
- políticas de persistencia e indexación.

### Container

Elemento estructural que contiene otros elementos.

Ejemplos:

- section;
- fieldset;
- row;
- columns;
- panel;
- step;
- repeatable group.

### Option

Valor seleccionable.

Distingue `label` y `value`.

### OptionSet

Colección reutilizable y versionable de opciones.

### Rule

`WHEN conditions THEN effects`.

### Condition

Predicado evaluable contra valores y contexto autorizado.

### Effect

Cambio declarativo aplicado cuando una Rule se cumple.

### DataSource

Proveedor de valores dinámicos.

### Validation

Regla de aceptación de un valor o conjunto de valores.

### FormAction

Operación posterior a una submission válida.

### Submission

Cabecera de un envío.

### SubmissionPayload

Representación canónica íntegra de valores normalizados y metadatos permitidos.

### SubmissionIndexValue

Proyección tipada destinada a búsqueda/filtrado, no fuente primaria de verdad.

### SubmissionFile

Metadatos y referencia a fichero.

### ActionRun

Ejecución concreta de una Action sobre una Submission.

### EmailTemplate

Plantilla reutilizable.

### FormTemplate

Punto de partida para crear un formulario; no herencia viva.

### AuditEvent

Evento administrativo relevante.

## 2. Identidad

Las referencias internas entre entidades lógicas DEBEN utilizar UUID o identificadores internos estables, nunca labels.

Cambiar:

`Provincia` → `Provincia de residencia`

no puede romper reglas.

El machine name es una identidad amigable para integraciones, pero no sustituye al UUID.

## 3. Machine names

Ejemplos:

- `email`;
- `provincia`;
- `tipo_usuario`.

DEBEN ser únicos dentro de un formulario.

Cambiar un machine name ya publicado DEBE generar una advertencia por posible impacto en:

- exportaciones;
- emails;
- webhooks;
- integraciones;
- filtros guardados.

## 4. Estados de Form

Como mínimo:

- draft;
- published;
- unpublished;
- archived;
- trashed.

Podrá disponer de:

- inicio de publicación;
- fin de publicación;
- access level;
- restricciones adicionales.

Despublicar DEBE impedir tanto renderizado autorizado como POST directo.

## 5. Estados de Submission

Inicialmente:

- new;
- viewed;
- processed;
- spam;
- archived;
- error.

La arquitectura permitirá estados adicionales o workflows futuros.

## 6. Versionado

Editar un formulario publicado DEBE modificar un borrador, no la versión activa.

Publicar DEBE:

1. compilar;
2. validar;
3. crear una nueva FormVersion;
4. activar esa versión.

Restaurar una versión histórica crea un nuevo borrador; no reescribe el snapshot antiguo.

## 7. Integridad histórica

Toda Submission DEBE conservar referencia a la FormVersion exacta usada.

Una respuesta antigua debe seguir siendo interpretable aunque posteriormente se:

- renombren labels;
- eliminen campos;
- cambien opciones;
- cambien emails;
- cambien reglas;
- cambien textos de consentimiento.

## 8. Concurrencia de versiones

Si un visitante carga v7 y durante la cumplimentación se publica v8:

- el POST identificará la versión de origen;
- el servidor decidirá según política si v7 continúa admitida;
- si se acepta, validará contra v7;
- registrará la Submission contra v7.

La política predeterminada debería permitir una ventana razonable para completar formularios ya iniciados, salvo que v7 haya sido revocada por seguridad.

## 9. Eliminación

Papelera y eliminación definitiva son fases distintas.

Antes del borrado definitivo se evaluarán:

- submissions;
- Menu Items;
- módulos;
- recursos;
- referencias;
- archivos.

No habrá borrado destructivo silencioso.


---

<!-- SOURCE: 03_FORM_SPEC.md -->

# 03 — FormSpec


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Propósito

`Nicode EasyForms FormSpec` será el contrato lógico, portable y versionado que describe un formulario ejecutable.

No será un volcado de tablas SQL.

Servirá de frontera entre:

- Authoring Model;
- Compiler;
- Renderer;
- Rule Engine;
- Validation Engine;
- Submission Engine;
- import/export;
- histórico.

## 2. Versiones diferentes

Se distinguirán siempre:

1. versión de Nicode EasyForms;
2. versión del schema FormSpec;
3. revisión/version del formulario.

Ejemplo:

- EasyForms `1.4.0`;
- FormSpec schema `1.1`;
- Form revision `27`.

## 3. Contenido mínimo

Un FormSpec deberá poder representar:

- metadata;
- publication/access policy;
- layout tree;
- elements;
- fields;
- Field Type properties;
- validators;
- local options;
- OptionSet references/version;
- Data Source references;
- rules;
- conditions;
- effects;
- actions;
- post-submit behavior;
- messages;
- presentation;
- privacy;
- retention;
- upload policy;
- anti-spam/CAPTCHA policy;
- submission storage/index policy;
- translations/references;
- compatibility metadata.

## 4. Propiedades del contrato

FormSpec DEBE ser:

- determinista;
- serializable;
- validable;
- versionable;
- migrable;
- independiente del HTML final;
- independiente del ID DOM;
- independiente del canal de renderizado;
- inmutable una vez publicado.

## 5. Form Compiler

El Compiler deberá:

- verificar estructura;
- comprobar identificadores;
- comprobar unicidad de machine names;
- resolver referencias;
- validar capacidades de cada Field Type;
- comprobar validators;
- comprobar Option Sets;
- comprobar Data Sources;
- comprobar Rule operators;
- comprobar Rule effects;
- detectar dependencias circulares;
- detectar conflictos bloqueantes;
- comprobar Actions;
- comprobar post-submit;
- comprobar políticas de almacenamiento;
- comprobar CAPTCHA configurado;
- emitir warnings de accesibilidad/seguridad;
- normalizar el contrato;
- producir hash;
- generar snapshot.

## 6. Severidad del diagnóstico

- `ERROR`: impide publicar.
- `WARNING`: permite publicar con confirmación/política.
- `INFO`: recomendación o información.

Ejemplos de ERROR:

- referencia inexistente;
- dependencia circular no resoluble;
- `min > max`;
- Action obligatoria incompleta;
- machine name duplicado;
- OptionSet requerido inexistente;
- tipo de dato incompatible con una Rule.

## 7. Migraciones

Cada versión de schema FormSpec deberá tener una estrategia de migración.

Una actualización del package no deberá convertir silenciosamente un FormSpec antiguo en algo semánticamente diferente.

## 8. FormSpec y secretos

FormSpec portable NO DEBE incluir secretos en claro.

Las referencias a:

- credenciales;
- API keys;
- passwords;
- tokens;

deberán resolverse mediante configuración protegida o referencias a secret/config providers.


---

<!-- SOURCE: 04_FIELD_TYPE_REGISTRY.md -->

# 04 — Field Type Registry


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Objetivo

La capacidad de admitir "todos los inputs actuales y futuros" no se resolverá con una lista rígida.

Cada Field Type será una capacidad registrada.

Un Field Type deberá declarar:

- identifier;
- categoría;
- schema de propiedades;
- tipo lógico de valor;
- renderer;
- normalizador;
- serializer;
- validator;
- operadores de Rule admitidos;
- capacidades de indexación;
- soporte de default/prefill;
- soporte de readonly/disabled;
- soporte de múltiples valores;
- configuración administrativa;
- requisitos de assets;
- compatibilidad de exportación.

## 2. Field Types estándar

### Texto

- text;
- textarea;
- email;
- telephone;
- url;
- search;
- password;
- hidden.

### Numéricos

- integer;
- decimal;
- number;
- currency;
- range.

### Fecha y tiempo

- date;
- time;
- datetime-local;
- month;
- week.

### Selección

- select;
- multiselect;
- radio;
- checkbox;
- checkbox group;
- toggle;
- yes/no;
- button group.

### Archivos

- file;
- multiple files.

### Otros HTML

- color.

### Funcionales

- consent;
- captcha placement/system captcha element;
- anti-spam marker cuando proceda;
- calculated/read-only value;
- hidden/system value.

### Presentación

No son campos de entrada, pero se gestionarán en el mismo Builder:

- heading;
- subheading;
- paragraph/text;
- safe HTML;
- separator;
- spacer;
- notice/help block.

## 3. Campos compuestos

Conceptos como:

- nombre completo;
- dirección;
- contacto;
- rango de fechas;

DEBERÍAN modelarse como presets que crean varios campos primitivos cuando eso facilite búsqueda, reglas y exportación.

## 4. Propiedades comunes

- UUID;
- machine name;
- label;
- admin label opcional;
- description;
- help;
- placeholder;
- default;
- required;
- readonly;
- disabled;
- autocomplete;
- inputmode;
- width/layout;
- CSS class controlada;
- initial visibility;
- persist value;
- include in notification;
- include in exports;
- searchable/indexed;
- sensitive;
- translatable properties.

## 5. Restricciones específicas

Cuando corresponda:

- min/max length;
- regex/pattern seguro;
- min/max numeric;
- step;
- decimal scale/precision;
- min/max date;
- min/max time;
- allowed MIME;
- allowed extension;
- max file size;
- max files;
- min/max selections.

## 6. Prefill

Fuentes permitidas de valor inicial:

- constante;
- usuario Joomla;
- contexto autorizado;
- query parameter explícitamente permitido;
- otro campo;
- Data Source.

Todo prefill se normaliza y valida.

## 7. Indexabilidad

Cada Field Type declarará cómo puede indexarse:

- keyword;
- text;
- integer;
- decimal;
- boolean;
- date;
- datetime;
- multi-value;
- non-indexable.

El Builder podrá permitir elegir si un campo debe participar en búsquedas/filtrado, dentro de las capacidades del tipo.

## 8. Extensibilidad

Añadir un nuevo Field Type NO DEBE exigir:

- alterar `forms`;
- crear columnas en `submissions`;
- modificar cada formulario;
- añadir `if field_type == ...` dispersos por todo el core.

Debe registrarse una implementación que cumpla el contrato.


---

<!-- SOURCE: 05_LAYOUT_SPEC.md -->

# 05 — Layout y estructura


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Tipos estructurales

El formulario podrá contener:

- section;
- group;
- fieldset;
- row;
- columns;
- panel;
- step;
- repeatable group;
- presentation elements;
- fields.

## 2. Árbol

La estructura será un árbol ordenado.

Cada nodo tendrá:

- UUID;
- parent;
- type;
- position/order;
- propiedades;
- reglas de visibilidad si corresponden.

Se permitirá anidación cuando el tipo de container la soporte.

## 3. Responsive

Los elementos podrán definir anchuras por breakpoint conceptual:

- desktop;
- tablet;
- mobile.

El FormSpec no debe almacenar clases Bootstrap obligatorias. El Renderer traduce el layout a HTML/CSS compatible con el frontend.

## 4. Builder

El Builder deberá ofrecer:

- drag & drop;
- insertar;
- mover;
- reordenar;
- duplicar;
- copiar/pegar cuando se implemente;
- eliminar;
- contraer;
- vista árbol;
- inspector de propiedades;
- selección múltiple futura.

## 5. Agrupación semántica

`fieldset`/`legend` deberán utilizarse cuando exista agrupación semántica de controles, especialmente radio/checkbox groups.

Un grupo visual no debe confundirse necesariamente con un fieldset semántico.

## 6. Formularios multipaso

Un formulario podrá tener pasos.

Cada Step:

- title;
- description;
- rules;
- validation boundary;
- previous/next;
- progress metadata.

Antes de avanzar se validarán los campos activos del paso según política.

Una Rule podrá ocultar un Step completo.

## 7. Repeatable groups

La arquitectura contemplará grupos repetibles:

- mínimo de repeticiones;
- máximo;
- botón añadir/quitar;
- validación por instancia;
- identidad de cada instancia;
- serialización inequívoca.

Si no entran en la primera release, el FormSpec no debe bloquear su incorporación.

## 8. Preview

La Preview administrativa DEBE utilizar el mismo Renderer.

Modos de viewport:

- desktop;
- tablet;
- mobile.

No deberá existir un renderer paralelo "solo de preview".


---

<!-- SOURCE: 06_VALIDATION_SPEC.md -->

# 06 — Validación y normalización


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Principio

La validación de navegador mejora UX; la validación de servidor decide.

Cada valor seguirá:

raw input  
→ canonical input extraction  
→ normalization  
→ Rule evaluation/context  
→ type validation  
→ field validators  
→ cross-field validators  
→ accepted canonical value.

## 2. Validadores estándar

- required;
- type;
- min length;
- max length;
- pattern;
- min;
- max;
- step;
- integer;
- decimal precision/scale;
- date min/max;
- time min/max;
- min selections;
- max selections;
- allowed file extension;
- allowed MIME;
- max size;
- max file count.

## 3. Validación entre campos

Debe soportar:

- A = B;
- A != B;
- A > B;
- A < B;
- A >= B;
- A <= B;
- fecha A antes de B;
- fecha A después de B;
- al menos uno en conjunto;
- exactamente N;
- rango coherente;
- confirmación de valor.

## 4. Mensajes

Cada validador podrá tener:

- mensaje por defecto traducible;
- override a nivel de formulario;
- override a nivel de campo/regla.

No se expondrá información técnica sensible.

## 5. Campos inactivos

Después de ejecutar el Rule Engine servidor:

- un campo no activo no debe considerarse requerido;
- por defecto, valores enviados para campos inactivos se ignorarán y no persistirán;
- una política futura podría permitir conservarlos explícitamente, pero deberá ser consciente y documentada.

## 6. Valores de selección

El servidor DEBE comprobar que los valores recibidos pertenecen a:

- opciones estáticas válidas;
- OptionSet exacto;
- resultado válido de Data Source para el contexto;
- conjunto permitido por reglas.

Un `<select>` manipulado no puede introducir valores arbitrarios.

## 7. Normalización

Ejemplos:

- strings: política de trim definida;
- email: forma canónica conservando el valor válido;
- integer/decimal: conversión tipada;
- date/datetime: representación canónica;
- boolean: representación inequívoca;
- multi-value: array normalizado;
- files: metadata controlada.

No se aplicará un "sanitizado genérico" como sustituto de validación por tipo.

## 8. Errores

La respuesta de validación estructurada deberá contener:

- error code;
- field UUID/machine name cuando proceda;
- mensaje de usuario;
- severidad;
- metadata no sensible.

El frontend podrá mostrar:

- resumen;
- mensaje junto al campo;
- foco en primer error.


---

<!-- SOURCE: 07_RULE_ENGINE_SPEC.md -->

# 07 — Rule Engine


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Modelo

Una Rule sigue:

`WHEN <condition tree> THEN <effects>`

Las condiciones admiten:

- AND;
- OR;
- grupos anidados;
- negación controlada cuando corresponda.

## 2. Fuentes de condición

- valor de campo;
- estado de campo;
- usuario autenticado;
- idioma;
- fecha/hora;
- canal/contexto;
- otros contextos registrados.

No se permite acceso genérico a variables PHP.

## 3. Operadores

Según Field Type:

- equals;
- not equals;
- contains;
- not contains;
- starts with;
- ends with;
- in;
- not in;
- empty;
- not empty;
- selected;
- not selected;
- greater than;
- less than;
- greater/equal;
- less/equal;
- between;
- before;
- after;
- safe pattern match.

El Field Type Registry determina compatibilidad.

## 4. Effects

- show field;
- hide field;
- show group/container;
- hide group/container;
- show/hide step;
- enable;
- disable;
- required;
- optional;
- set value;
- clear value;
- change/filter options;
- change default;
- activar/desactivar Action cuando sea parte del modelo de Action condition.

## 5. Opciones dinámicas

Una regla podrá provocar que un campo:

- cambie OptionSet;
- aplique filtro;
- envíe parámetros a Data Source;
- se vacíe si su valor deja de ser válido.

## 6. Prioridad

Cada Rule tendrá:

- enabled;
- priority;
- deterministic order.

Se definirá una semántica explícita para efectos múltiples sobre el mismo target.

## 7. Conflictos

El Compiler debe detectar conflictos inequívocos.

Ejemplo bloqueante:

- misma prioridad;
- misma condición;
- mismo target;
- `required`;
- `optional`.

Otros conflictos pueden ser warnings si el orden los hace deterministas.

## 8. Dependencias circulares

Se construirá un grafo de dependencias.

Ciclos que hagan indeterminada la evaluación impedirán publicación.

## 9. Cliente y servidor

Habrá dos evaluadores semánticamente equivalentes:

### Client Rule Engine
UX inmediata.

### Server Rule Engine
autoridad.

Manipular JavaScript no permitirá eludir reglas.

## 10. Estabilización

Las Rules que cambian valores/opciones pueden provocar nuevas Rules.

El Engine deberá evaluar hasta estado estable con:

- orden determinista;
- límite de iteraciones;
- detección de ciclo/no convergencia.

Una no convergencia será error de configuración.


---

<!-- SOURCE: 08_OPTIONS_DATASOURCES_SPEC.md -->

# 08 — Options y Data Sources


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Option

Cada opción tendrá:

- ID/UUID;
- internal value;
- label;
- order;
- enabled;
- default flag;
- metadata opcional.

`label` y `value` nunca deben confundirse.

Ejemplo:

- label: `España`;
- value: `ES`.

## 2. Opciones locales

Adecuadas para listas propias de un formulario.

## 3. Option Sets

Recursos reutilizables:

- países;
- provincias;
- departamentos;
- especialidades;
- sí/no;
- clasificaciones internas.

Serán versionables.

Un FormSpec publicado debe apuntar a una versión determinada o incorporar snapshot suficiente para conservar semántica histórica.

## 4. Dependencias

Debe soportarse:

- País → Provincia;
- Provincia → Municipio;
- Categoría → Familia → Producto.

Una fuente puede depender de uno o varios campos.

## 5. Data Source Registry

Cada Data Source Provider declarará:

- identifier;
- config schema;
- input parameters;
- dependency parameters;
- output schema;
- value mapping;
- label mapping;
- cache capability;
- TTL;
- timeout;
- failure mode.

## 6. Fuentes iniciales

- static/local;
- OptionSet;
- entidades Joomla aprobadas;
- provider personalizado;
- HTTP/API provider futuro.

No habrá un textarea de SQL libre como funcionalidad estándar.

## 7. Seguridad

Un valor devuelto al navegador no se convierte por ello en confiable.

Al submit, el servidor deberá revalidar la opción contra la fuente correspondiente o contra snapshot/política válida.

## 8. Caché

Cada provider declara si admite cache y qué elementos del contexto forman parte de la cache key.

No se cachearán resultados dependientes de información sensible de distintos usuarios bajo una clave compartida incorrecta.

## 9. Errores

Políticas posibles:

- fail closed y mostrar error;
- lista vacía;
- fallback definido;
- retry limitado para llamadas remotas.

La semántica deberá configurarse explícitamente.


---

<!-- SOURCE: 09_SUBMISSION_SPEC.md -->

# 09 — Submission Engine


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Responsabilidad

El Submission Engine recibe una petición y decide si genera una Submission válida, consistente, persistida según política y procesada por Actions.

## 2. Pipeline normativo

1. resolver Form y FormVersion;
2. comprobar disponibilidad;
3. comprobar access level/usuario;
4. comprobar origen/contexto;
5. comprobar CSRF cuando proceda;
6. ejecutar política CAPTCHA/anti-spam;
7. comprobar límites/rate policies;
8. extraer payload permitido;
9. normalizar;
10. ejecutar Rule Engine servidor;
11. resolver opciones activas;
12. validar campos;
13. validar relaciones entre campos;
14. validar y almacenar temporalmente uploads;
15. generar idempotency/attempt semantics;
16. persistir Submission si procede;
17. persistir índice de búsqueda si procede;
18. finalizar archivos;
19. ejecutar Action Engine;
20. calcular respuesta post-submit;
21. registrar observabilidad/auditoría necesaria.

## 3. Modos de persistencia

Cada formulario podrá definir:

- almacenar submission completa;
- no almacenar respuestas tras procesamiento;
- almacenar solo metadata mínima;
- política especial aprobada.

El valor predeterminado del producto se definirá en configuración global y podrá sobrescribirse por Form.

## 4. Submission canónica

Cuando se almacene, debe preservar:

- Submission UUID;
- Form;
- FormVersion;
- timestamp;
- valores normalizados;
- labels/metadata necesarios o resolubles desde snapshot;
- archivos;
- contexto permitido;
- resultado de procesamiento;
- Action status.

## 5. Idempotencia

El sistema debe mitigar dobles envíos accidentales.

Cada render/attempt podrá incorporar un token/identifier.

La idempotencia debe distinguir:

- retry técnico de la misma petición;
- una segunda submission legítima.

## 6. AJAX y no AJAX

Ambos modos usarán el mismo pipeline.

AJAX solo cambia el transporte/presentación de la respuesta.

## 7. Estado y Action status

El éxito de persistencia y el éxito de las Actions son dimensiones diferentes.

Ejemplo:

- Submission guardada: sí.
- Email notificación: falló.
- Webhook: correcto.

La UI debe representar esa diferencia.

## 8. Formularios despublicados

El POST siempre vuelve a comprobar estado y permisos.

Haber renderizado anteriormente un formulario no concede derecho perpetuo a enviarlo.

## 9. Límites

Políticas futuras/initiales configurables:

- unlimited;
- one per user;
- one per session;
- global maximum;
- date window;
- optional custom limiter provider.

## 10. Contexto de canal

Se registrará de forma controlada si la submission vino de:

- component page;
- module;
- future content plugin;
- API futura.

Nunca se confiará en un channel enviado libremente por el navegador sin validación.


---

<!-- SOURCE: 10_ACTION_ENGINE_SPEC.md -->

# 10 — Action Engine


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Concepto

Después de una Submission válida pueden ejecutarse cero, una o múltiples Actions.

Cada Action tendrá:

- UUID;
- type;
- enabled;
- order;
- condition;
- config;
- failure policy;
- retry policy cuando sea compatible.

## 2. Actions iniciales

- persist submission;
- email notification;
- email autoresponse/acknowledgement;
- redirect/post-submit navigation;
- webhook HTTP.

La persistencia puede modelarse como fase del pipeline y exponerse conceptualmente como Action/configuración; la implementación final deberá mantener atomicidad y semántica claras.

## 3. Actions futuras

El Registry permitirá:

- CRM;
- ERP;
- newsletter;
- Slack/Teams;
- user creation;
- content creation;
- ticketing;
- external API;
- custom providers.

## 4. Conditions

Una Action puede ejecutarse solo si se cumplen condiciones.

Ejemplo:

- si `tipo = comercial`, email a ventas;
- si `tipo = soporte`, email a soporte;
- si país = ES, webhook A;
- si país = PT, webhook B.

Las condiciones reutilizarán semántica compatible con Rule Engine sin permitir lógica arbitraria.

## 5. Failure policy

### Blocking

El fallo afecta al resultado global según transacción/política.

### Non-blocking

La Submission puede considerarse recibida; el fallo queda registrado y reintentable.

Cada tipo de Action debe declarar qué modos admite.

## 6. ActionRun

Cada ejecución registra:

- action;
- submission;
- attempt;
- started_at;
- finished_at;
- status;
- result code;
- mensaje técnico saneado;
- retry eligibility.

## 7. Reintentos

Debe permitirse reintentar una Action fallida compatible sin repetir las que ya finalizaron correctamente.

La administración ofrecerá:

- retry individual;
- retry de fallidas de una Submission;
- acciones masivas controladas futuras.

## 8. Email Action

Configurable:

- to;
- cc;
- bcc;
- reply-to;
- subject;
- HTML;
- plain text;
- template;
- campos incluidos;
- attachments permitidos;
- conditions.

El remitente debe proceder de una configuración segura, no de una dirección arbitraria del visitante.

Un email de usuario validado podrá usarse como Reply-To.

## 9. Tokens

Las plantillas usarán tokens declarativos:

- form;
- submission;
- date;
- user;
- field value;
- selected option label;
- response summary.

No PHP ejecutable.

## 10. Webhook

Debe contemplar:

- URL permitida/configurada;
- method;
- headers seguros;
- payload mapping;
- timeout;
- firma opcional;
- retry;
- bloqueo SSRF;
- logging sin secretos.

## 11. Orden

Las Actions se ejecutan en orden definido y la política decidirá si un fallo blocking detiene el resto.

La semántica debe ser visible en Administrator.


---

<!-- SOURCE: 11_ADMIN_UX_SPEC.md -->

# 11 — Administrator UX


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Menú principal

`Nicode EasyForms`

- Panel de control
- Formularios
- Envíos
- Recursos
- Registros
- Configuración
- Información del sistema

## 2. Dashboard

Debe mostrar información operativa, no solo accesos:

- formularios publicados;
- formularios despublicados;
- borradores;
- submissions recientes;
- submissions 7/30 días;
- spam;
- errores;
- Actions fallidas;
- emails fallidos;
- webhooks fallidos;
- formularios inválidos;
- últimas modificaciones;
- volumen de almacenamiento;
- jobs/exportaciones en curso o pendientes cuando exista subsystem de jobs.

Alertas:

- Action incompleta;
- Data Source desactivada;
- CAPTCHA seleccionado no disponible;
- versión con warning;
- ficheros huérfanos;
- errores repetidos;
- índice de búsqueda pendiente.

## 3. Formularios — listado

Columnas mínimas:

- selección;
- state;
- name;
- alias;
- ID;
- published version;
- field count;
- submission count;
- modified;
- author;
- language;
- access.

Filtros:

- search;
- state;
- language;
- access;
- author;
- tag/categoría administrativa futura.

Acciones:

- new;
- edit;
- publish;
- unpublish;
- duplicate;
- archive;
- trash;
- export;
- import.

## 4. Editor de formulario

Pestañas/áreas:

- General
- Constructor
- Lógica
- Acciones
- Datos y privacidad
- Presentación
- Publicación
- Permisos
- Versiones
- Vista previa
- Validación/diagnóstico

## 5. Constructor

Tres áreas principales:

### Paleta
Tipos disponibles.

### Canvas
Estructura.

### Inspector
Propiedades del elemento seleccionado.

Debe haber vista árbol para formularios complejos.

## 6. Publicación

`Publish` ejecuta `Compile & Publish`.

Los errores de compilación se presentan con:

- código;
- descripción;
- elemento afectado;
- enlace/navegación al elemento cuando sea posible.

## 7. Versiones

Listado:

- revision;
- date;
- author;
- comment;
- state;
- submission count;
- compare;
- preview;
- restore.

`Restore` crea nuevo draft.

## 8. Envíos — diseño de alto volumen

La vista de submissions debe estar pensada para cientos o millones de filas.

Debe permitir:

- selector de Form;
- date range;
- state;
- processing/action state;
- user;
- channel;
- free search cuando el SearchProvider lo soporte;
- filtros por campos indexados del formulario;
- filtros guardados;
- columnas configurables;
- orden por columnas autorizadas;
- navegación eficiente;
- selección de filas;
- detalle en panel/página;
- exportación según filtro;
- acciones masivas seguras.

No debe intentar cargar todas las respuestas ni construir una tabla con todos los campos de todos los formularios simultáneamente.

## 9. Vista contextual por formulario

Al seleccionar un formulario:

- la tabla puede mostrar columnas relevantes de ese Form;
- aparecen sus campos indexables como filtros;
- se usan labels de la versión/contexto actual con indicación histórica cuando haga falta;
- se puede abrir la Submission conservando la interpretación de su FormVersion.

## 10. Detalle de Submission

Áreas:

- Overview
- Respuestas
- Archivos
- Actions
- Historial
- Notas
- Datos técnicos autorizados

Debe mostrar:

- Submission ID/UUID;
- Form;
- FormVersion;
- received_at;
- estado;
- usuario si procede;
- canal;
- respuestas agrupadas según layout histórico;
- ActionRuns;
- errores;
- auditoría.

## 11. Acciones masivas

Como mínimo:

- cambiar estado;
- archive;
- export;
- anonymize cuando proceda;
- delete conforme ACL/política.

Para volúmenes grandes, una acción masiva deberá poder ejecutarse como job por criterio/filtro, no enviando millones de IDs en el navegador.

## 12. Recursos

Subsecciones:

- Option Sets;
- Data Sources;
- Email Templates;
- Form Templates.

## 13. Registros

Separar:

- technical logs;
- audit log;
- Action failures;
- job history.

## 14. Configuración

Secciones:

- General
- Submissions
- Search/index
- Security
- CAPTCHA/anti-spam defaults
- Email
- Files
- Retention
- Logs
- Performance
- Development/diagnostics

## 15. Información del sistema

Mostrar:

- EasyForms package/component/module/library version;
- DB schema version;
- supported FormSpec versions;
- Joomla version;
- PHP version;
- DB driver;
- mail availability;
- upload storage;
- cache;
- CAPTCHA providers detectados;
- filesystem permissions;
- cron/scheduler/CLI capabilities si se usan;
- health checks.


---

<!-- SOURCE: 12_FRONTEND_SPEC.md -->

# 12 — Frontend


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Publicación como página

El componente expondrá un Menu Item Type:

`Nicode EasyForms → Formulario`

Parámetro principal:

- `Formulario`: selector dinámico de formularios utilizables.

El Item de menú almacena el identificador del formulario, no una copia de sus campos.

## 2. Publicación como módulo

`mod_nicode_easy_forms`

Parámetro principal:

- Formulario.

Parámetros secundarios, exclusivamente de contexto/presentación:

- mostrar título;
- mostrar descripción;
- class suffix/controlado;
- layout;
- comportamiento si no está disponible.

El módulo NO puede redefinir reglas, destinatarios, validadores o estructura.

## 3. Form no disponible

Si un Menu Item o module referencia un Form:

- unpublished;
- fuera de fechas;
- sin permiso;
- inexistente;

el runtime aplica una política definida:

- 404;
- mensaje de indisponibilidad;
- módulo no renderizado.

El POST se rechaza independientemente de lo que hubiera mostrado una caché antigua.

## 4. Múltiples instancias

Cada render obtiene `instance_id`.

El DOM ID se deriva de:

- form identity;
- field identity;
- instance identity.

Dos instancias del mismo Form pueden coexistir sin:

- IDs duplicados;
- rules cruzadas;
- CAPTCHA namespace conflictivo;
- mensajes cruzados.

## 5. Assets

Se cargarán mediante Web Asset Manager.

Solo se activarán cuando exista una instancia que los necesite.

Dependencias declaradas en `joomla.asset.json`.

## 6. Presentación

Por defecto se hereda el template Joomla.

EasyForms aporta estilos mínimos estructurales.

Opciones:

- labels top/side;
- required marker;
- help position;
- spacing;
- columns;
- progress;
- validation summary;
- success/error container.

No se impondrá un framework CSS externo.

## 7. Submit

Soportar:

- traditional POST;
- AJAX.

Ambos contra controller/ruta Joomla y mismo Submission Engine.

## 8. Estados UX

Como mínimo:

- initial;
- validating;
- submitting;
- success;
- validation error;
- anti-spam/captcha error;
- transient processing error;
- unavailable.

## 9. JS

JavaScript se limitará a:

- client validation UX;
- Rule Engine cliente;
- dependent options;
- AJAX;
- progressive enhancement;
- accessibility behavior.

La seguridad y la verdad del estado permanecen en servidor.

## 10. Sin JS

Se definirá qué funcionalidades soportan fallback sin JavaScript.

Los formularios simples deberían poder enviarse; funcionalidades altamente dinámicas podrán requerir JS si la especificación lo declara, pero el sistema nunca confiará en JS para validar.


---

<!-- SOURCE: 13_SECURITY_SPEC.md -->

# 13 — Seguridad


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Principios

- deny by default;
- server authoritative;
- least privilege;
- contextual escaping;
- typed validation;
- secrets out of FormSpec portable;
- no arbitrary executable code.

## 2. Amenazas a cubrir

- CSRF;
- XSS almacenado/reflejado;
- SQL injection;
- parameter tampering;
- option tampering;
- rule bypass;
- ACL bypass;
- direct POST a Form despublicado;
- malicious uploads;
- path traversal;
- email header injection;
- SSRF en webhooks/Data Sources;
- replay/double submit;
- brute spam;
- over-posting;
- mass assignment;
- information leakage;
- insecure direct object references;
- export abuse;
- formula injection en CSV;
- log injection;
- secret leakage.

## 3. Input

No se sobrescribirá `$_POST`.

Se extraerán exclusivamente keys esperadas según FormSpec.

Campos desconocidos serán ignorados o rechazados según política.

## 4. SQL

Consultas parametrizadas y APIs DatabaseInterface.

Los identificadores dinámicos deben proceder de allowlists internas; los valores se bindearán.

## 5. Output

Escaping contextual:

- HTML text;
- attribute;
- URL;
- JSON;
- email;
- CSV/export.

Safe HTML será una capacidad explícita y restringida de contenido administrativo, nunca una excusa para imprimir input del visitante sin escapar.

## 6. CSRF

Se utilizarán los mecanismos de token/sesión de Joomla donde el flujo de sesión lo permita.

CSRF y CAPTCHA son controles distintos.

## 7. ACL

Toda acción administrativa comprueba permiso en servidor.

Ocultar botones no sustituye autorización.

## 8. Uploads

- allowlist extension;
- MIME inspection;
- size;
- count;
- generated storage names;
- storage no público/predecible;
- no ejecución;
- download controller con ACL;
- optional malware scanner provider futuro.

## 9. Email

- sender configurado;
- Reply-To validado;
- no concatenar headers con input arbitrario;
- recipient sources controladas;
- tokens escapados según contexto.

## 10. Webhooks y HTTP Data Sources

Mitigación SSRF:

- esquemas permitidos;
- bloqueo de loopback/link-local/private cuando corresponda;
- redirects controlados;
- timeouts;
- límites;
- DNS/rebinding considerations en implementación;
- secretos no logados.

## 11. Exports

CSV debe mitigar formula injection para valores que comienzan con caracteres peligrosos según política de exportación.

Las exportaciones respetan ACL y datos sensibles.

## 12. CAPTCHA

EasyForms NO implementará un algoritmo CAPTCHA propio.

Se integrará con el sistema/provider de CAPTCHA Joomla según `25_JOOMLA_CAPTCHA_ANTISPAM_SPEC.md`.

## 13. Rate limiting

Si Joomla no ofrece un mecanismo genérico aplicable al caso, EasyForms podrá implementar un limiter propio como control anti-abuso, desacoplado mediante servicio/provider. No debe confundirse con CAPTCHA.

## 14. Logs

No registrar por defecto:

- password;
- tokens;
- secrets;
- payload completo;
- datos sensibles de campos.

## 15. Security headers

EasyForms no debe romper CSP u otras políticas del sitio mediante inline JS innecesario. Assets y scripts deben diseñarse para integrarse con la política del sitio.


---

<!-- SOURCE: 14_PRIVACY_RETENTION_SPEC.md -->

# 14 — Privacidad, retención y datos sensibles


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Configuración por Form

- store submissions;
- store authenticated user relation;
- IP storage policy;
- User-Agent policy;
- retention;
- automatic deletion;
- automatic anonymization;
- sensitive fields;
- export visibility;
- email inclusion.

## 2. Minimización

La recogida de IP y User-Agent estará desactivada por defecto salvo decisión explícita de producto/configuración.

No se debe almacenar información técnica "por si acaso".

## 3. Sensitive field

Un Field marcado como sensible:

- no aparece en technical logs;
- puede excluirse de emails;
- puede excluirse de exports;
- puede requerir permiso específico de visualización;
- puede tener retención distinta;
- puede quedar fuera del índice de búsqueda.

## 4. Consent Field

Debe registrar de manera interpretable:

- accepted/not accepted;
- consent text/version reference;
- timestamp de submission;
- FormVersion.

Así puede conocerse qué texto se mostró cuando se obtuvo el consentimiento.

## 5. Retention

Políticas:

- indefinite explícito;
- N días/meses/años;
- delete;
- anonymize.

Los jobs de retención deben ser idempotentes y auditables.

## 6. Derecho operativo de eliminación/anonimización

La UI debe poder:

- localizar submissions;
- anonimizar;
- eliminar conforme a permisos;
- registrar la acción administrativa.

## 7. Archivos

La retención de Submission y de sus files debe ser coherente.

Eliminar/anonymize debe tener reglas explícitas sobre:

- fichero;
- metadata;
- checksum;
- ActionRun logs.

## 8. Backups

La documentación deberá aclarar que eliminar de la base activa no equivale necesariamente a eliminar copias históricas del backup del sitio; las políticas de backup pertenecen también al operador del sitio.

## 9. FormSpec histórico

Preservar una FormVersion no obliga a preservar datos personales de submissions. Son dominios de retención separados.


---

<!-- SOURCE: 15_ACL_SPEC.md -->

# 15 — ACL


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Base

EasyForms utilizará ACL Joomla.

Permisos de componente iniciales:

- `core.admin`;
- `core.options`;
- `core.manage`;
- `core.create`;
- `core.edit`;
- `core.edit.state`;
- `core.delete`.

Permisos específicos propuestos:

- `easyforms.forms.manage`;
- `easyforms.forms.publish`;
- `easyforms.submissions.view`;
- `easyforms.submissions.manage`;
- `easyforms.submissions.export`;
- `easyforms.submissions.delete`;
- `easyforms.submissions.view_sensitive`;
- `easyforms.resources.manage`;
- `easyforms.logs.view`;
- `easyforms.jobs.manage`.

Los nombres definitivos se fijarán antes de código.

## 2. ACL por Form

Los Forms podrán actuar como assets hijos del componente.

Casos:

- equipo A administra Form A;
- equipo B administra Form B;
- compliance puede ver respuestas pero no editar Forms;
- marketing puede exportar solo ciertos Forms.

## 3. Frontend access

Cada Form tendrá Joomla access level y reglas adicionales cuando proceda.

Comprobar en:

- render;
- Data Source requests;
- submit;
- file download;
- submission view futura de frontend.

## 4. Administrator

Cada Controller comprueba autorización.

La View puede ocultar acciones no permitidas, pero eso es UX, no seguridad.

## 5. Datos sensibles

`view_sensitive` puede ser un permiso adicional al simple `submissions.view`.

La UI deberá enmascarar/ocultar campos sensibles a usuarios sin permiso.

## 6. Export

Exportar es una capacidad distinta de visualizar y debe tener permiso independiente.

## 7. Auditoría

Las operaciones privilegiadas deberán quedar registradas:

- export;
- delete;
- anonymize;
- retry Action;
- reveal sensitive;
- cambios de configuración.


---

<!-- SOURCE: 16_DATABASE_SPEC.md -->

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


---

<!-- SOURCE: 17_INSTALL_UPDATE_UNINSTALL_SPEC.md -->

# 17 — Instalación, actualización y desinstalación


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Package

Artefacto distribuible:

`pkg_nicode_easy_forms.zip`

Constituyentes iniciales:

- `com_nicode_easy_forms`;
- `mod_nicode_easy_forms`;
- `lib_nicode_easy_forms`.

Las dependencias internas deberán quedar declaradas.

## 2. Instalación

Proceso:

1. comprobar versión Joomla;
2. comprobar PHP;
3. comprobar DB soportada por nuestra matriz;
4. instalar library;
5. instalar component;
6. instalar module;
7. crear schema;
8. registrar schema version;
9. instalar configuración por defecto;
10. registrar assets/resources;
11. limpiar/inicializar caches necesarias;
12. health check;
13. mostrar resultado.

## 3. No depender de Compatibility Plugin

La compatibilidad Joomla 6 debe basarse en APIs actuales.

## 4. SQL

Habrá:

- install schema;
- update/migration scripts versionados;
- uninstall strategy.

Cambios de schema no se harán ad-hoc desde un Controller normal.

## 5. Actualizaciones

Una instalación puede saltar varias versiones.

Las migraciones deben aplicarse en orden.

Cada release puede incluir:

- DB migration;
- FormSpec migration;
- config migration;
- index rebuild requirement;
- background post-update job cuando un cambio sea demasiado grande para una petición de actualización.

## 6. Datos masivos durante updates

Nunca se asumirá que millones de submissions pueden reescribirse síncronamente durante la instalación.

Si una versión requiere:

- reindexar;
- recalcular;
- transformar payloads masivos;

se hará mediante proceso versionado, resumible e idempotente fuera del paso crítico de instalación.

## 7. Desinstalación

Configurable:

### Purge data
Eliminar:

- schema;
- cache;
- archivos propiedad inequívoca de EasyForms según política;
- temporales.

### Preserve data
Conservar:

- forms;
- versions;
- submissions;
- files según política;
- schema version.

Una reinstalación debe detectar datos conservados.

## 8. Almacenamiento externo

No borrar automáticamente objetos externos que no sean inequívocamente propiedad del package.

## 9. Integridad del package

Cuando sea soportado por el manifest/package, impedir desinstalar una pieza hija necesaria dejando el resto roto.

## 10. Uninstall warnings

Antes de un purge destructivo, Joomla debe mostrar información suficiente sobre:

- formularios;
- submissions;
- files;
- tamaño aproximado;
- irreversibilidad.

La confirmación final se implementará conforme a capacidades Joomla.


---

<!-- SOURCE: 18_EXTENSION_POINTS_SPEC.md -->

# 18 — Extension Points


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Objetivo

Extender EasyForms sin modificar core.

## 2. Registries

### FieldTypeRegistry
Campos.

### ValidatorRegistry
Validaciones.

### RuleOperatorRegistry
Comparadores.

### RuleEffectRegistry
Efectos.

### DataSourceRegistry
Fuentes.

### ActionRegistry
Acciones post-submit.

### StorageProviderRegistry
Files/payload storage si evoluciona.

### SearchProviderRegistry
Búsqueda de submissions.

## 3. Contratos

Cada provider debe declarar:

- ID estable;
- versión;
- config schema;
- capabilities;
- validation;
- lifecycle;
- security constraints;
- serializable metadata.

## 4. Eventos

Eventos conceptuales:

- BeforeFormRender;
- AfterFormRender;
- BeforeValidation;
- AfterValidation;
- BeforeSubmissionPersist;
- AfterSubmissionPersist;
- BeforeAction;
- AfterAction;
- ResolveDataSource;
- BeforeExport;
- AfterExport.

Los nombres y tipos finales deben ajustarse al sistema de eventos Joomla actual.

## 5. Plugins Joomla

Cuando una capacidad encaje naturalmente en el ecosistema Joomla, se preferirá un plugin/provider Joomla antes que un mecanismo paralelo.

CAPTCHA es caso obligatorio de esta estrategia.

## 6. Compatibility contracts

Un FormSpec publicado debe declarar dependencias de providers no-core.

Si falta un provider requerido:

- el Form no debe ejecutarse silenciosamente de forma incorrecta;
- Administrator debe mostrar error;
- render/submit debe fail closed según criticidad.

## 7. Versionado de provider

Cambios breaking deben versionarse.

El Compiler debe poder detectar configuración antigua incompatible.

## 8. No `if form_id`

Está prohibido ampliar el producto introduciendo lógica del tipo:

`if ($formId === 12) Ellipsis`

Las variaciones deben expresarse mediante datos o providers.


---

<!-- SOURCE: 19_I18N_SPEC.md -->

# 19 — Internacionalización


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Interfaz estática

Textos del package mediante language files Joomla.

Inicialmente:

- es-ES;
- en-GB recomendado como base técnica/distribución.

## 2. Contenido dinámico

Traducibles:

- form title;
- description;
- labels;
- placeholders;
- help;
- options;
- validation messages;
- success/error messages;
- email subjects/bodies;
- step titles;
- consent text.

## 3. Idioma base

Cada Form tendrá idioma/base y traducciones o estrategia definida.

## 4. Histórico

La FormVersion debe permitir saber qué contenido/traducción correspondía a la versión publicada.

## 5. Fallback

Orden de fallback documentado:

- idioma exacto;
- base language;
- form default;
- system default según diseño definitivo.

Nunca mostrar una key interna al visitante si existe fallback válido.

## 6. Search

La indexación de textos de respuesta no debe asumir una única lengua.

La normalización debe ser configurable por SearchProvider.

## 7. Date/number

Render y parsing deben respetar semántica de Field Type y locale sin perder representación canónica interna.


---

<!-- SOURCE: 20_ACCESSIBILITY_SPEC.md -->

# 20 — Accesibilidad


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Objetivo

WCAG 2.2 AA como objetivo de diseño y testing.

## 2. Campos

- label programáticamente asociado;
- help mediante `aria-describedby` cuando proceda;
- errores asociados;
- `aria-invalid`;
- required comunicado;
- instructions antes del input cuando corresponda.

## 3. Grupos

Radio/checkbox groups:

- fieldset;
- legend;
- navegación coherente.

## 4. Errores

Tras submit inválido:

- resumen accesible;
- links/foco a campos;
- primer error enfoc-able;
- mensajes no basados solo en color.

## 5. Lógica dinámica

Mostrar/ocultar:

- mantiene orden de foco;
- no deja foco atrapado;
- anuncia cambios relevantes cuando proceda;
- campos ocultos no deben seguir generando errores invisibles.

## 6. Multipaso

- step actual identificable;
- progreso comprensible;
- navegación teclado;
- validación comunicada;
- títulos claros.

## 7. CAPTCHA

La accesibilidad dependerá del provider Joomla seleccionado; EasyForms debe renderizarlo correctamente y no degradar su soporte.

## 8. Builder Administrator

El Builder deberá ofrecer alternativa suficiente a drag & drop mediante controles de mover/reordenar accesibles.

## 9. Contraste y CSS

EasyForms no fijará colores que impidan al template cumplir contraste.

## 10. Testing

Pruebas automatizadas donde sea posible + revisión manual de teclado, lector de pantalla y flujos dinámicos en releases relevantes.


---

<!-- SOURCE: 21_TEST_SPEC.md -->

# 21 — Testing


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Pirámide

### Unit
- compiler;
- validators;
- normalizers;
- Rule Engine;
- option resolver;
- FormSpec migration;
- search query builder;
- action policies.

### Integration
- database;
- Joomla ACL;
- mail;
- CAPTCHA adapter;
- storage;
- cache;
- HTTP providers.

### Functional/System
- Administrator;
- Builder;
- publish;
- module;
- menu item;
- submissions;
- search;
- exports;
- Actions.

### Security
- CSRF;
- XSS;
- SQL injection;
- option tampering;
- rule bypass;
- ACL bypass;
- malicious uploads;
- SSRF;
- header injection;
- CSV injection;
- direct POST.

### Performance
- form render;
- submit;
- search;
- deep browsing;
- exports;
- index rebuild.

## 2. Dataset de escala

Tests de performance deberán incluir datasets sintéticos:

- 100 forms;
- 1M submissions;
- distribución realista de fields indexados;
- varios millones de index rows;
- ActionRun history.

Se ampliará cuando se definan SLOs.

## 3. Criterios esenciales de aceptación

1. Crear Form sin código.
2. Formularios sin límite artificial.
3. Campos sin límite artificial.
4. Reordenar.
5. Agrupar.
6. Multipaso.
7. Validaciones.
8. Dependencias condicionales.
9. Opciones dependientes.
10. Despublicar.
11. POST a despublicado rechazado.
12. Menu Item lista Forms.
13. Menu Item renderiza Form.
14. Module lista Forms.
15. Module renderiza mismo runtime.
16. Dos instancias coexisten.
17. Validación cliente/servidor coherente.
18. Bypass JS no evita validación.
19. Opción manipulada se rechaza.
20. Draft no altera published.
21. Publish crea FormVersion.
22. Submission conserva FormVersion.
23. Store/no-store configurable.
24. Emails configurables.
25. Autorespuesta configurable.
26. Actions condicionales.
27. Action failure persistido.
28. Import/export.
29. Restore crea draft.
30. Install/update/uninstall nativos.
31. New Field Type sin schema por formulario.
32. New Action Type sin editar Forms existentes.
33. Ningún PHP por Form.
34. Views sin lógica de negocio significativa.
35. Input navegador no confiable.
36. Millón de submissions no obliga a cargar/scan completo.
37. Filtros por campos indexados usan índice.
38. Export masivo no requiere una única request web.
39. CAPTCHA usa provider Joomla.
40. Mensajes success/error son configurables.
41. El usuario puede consultar respuesta histórica correctamente.
42. Sensitive fields respetan ACL.
43. Búsqueda global respeta provider/capabilities.
44. Reindexar no altera canonical payload.
45. Un Action fallido puede reintentarse sin repetir exitosas.


---

<!-- SOURCE: 22_RELEASE_SPEC.md -->

# 22 — Release y Definition of Done


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Versionado

Semantic Versioning:

`MAJOR.MINOR.PATCH`.

## 2. Tres versiones relevantes

- package/software version;
- DB schema version;
- FormSpec schema version.

No deben confundirse.

## 3. Definition of Done de feature

Una feature no está terminada solo porque funcione manualmente.

Debe tener:

- requirement ID;
- spec actualizada;
- acceptance criteria;
- tests adecuados;
- implementación;
- ACL revisado;
- security review proporcional;
- i18n;
- accessibility cuando aplique;
- migration si aplica;
- docs;
- changelog.

## 4. Release checklist

- unit/integration/system tests;
- PHP/Joomla compatibility matrix;
- DB matrix declarada;
- package installation clean;
- upgrade from supported previous versions;
- uninstall modes;
- schema checks;
- FormSpec migration;
- assets build;
- language completeness;
- no deprecated APIs bloqueantes;
- security checks;
- performance smoke tests;
- changelog.

## 5. Breaking changes

Requieren:

- ADR;
- migration path;
- documentación;
- version bump acorde;
- compatibility analysis.

## 6. Soporte

`Information System` debe permitir recopilar diagnóstico sin exponer secretos ni payloads personales.

## 7. Release reproducible

El repositorio deberá definir cómo construir el package final a partir de source, assets y manifests.


---

<!-- SOURCE: 23_SUBMISSION_SEARCH_SCALE_SPEC.md -->

# 23 — Submissions, búsqueda y escala


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Requisito principal

EasyForms debe seguir siendo operable cuando existan:

- cientos de Forms;
- millones de Submissions;
- millones o decenas de millones de valores indexados;
- años de histórico.

"Operable" significa poder encontrar información sin descargarla completa ni recorrer manualmente páginas infinitas.

## 2. Separación obligatoria

### Canonical Submission Store
Preserva íntegramente la respuesta.

### Search Projection
Optimizada para consultar.

### Search Provider
Contrato que ejecuta búsqueda.

El índice es regenerable; el canonical payload no se deriva del índice.

## 3. Campos indexables

Cada field podrá declarar, según capacidades:

- searchable;
- filterable;
- sortable;
- facetable/aggregatable futuro;
- not indexed.

Defaults conservadores:

- IDs, email, estados y campos cortos pueden ser buenos candidatos;
- textareas largos no deberían crear automáticamente índices costosos sin necesidad;
- fields sensibles no se indexan por defecto.

## 4. Tipos indexados

No almacenar todo como string.

Tipos:

- keyword;
- text;
- integer;
- decimal;
- boolean;
- date;
- datetime;
- multi-value.

Esto permite comparaciones correctas.

## 5. Búsqueda administrativa

### Nivel global

Buscar por:

- Submission UUID/ID;
- Form;
- fechas;
- state;
- user;
- action status;
- texto global cuando provider lo soporte.

### Nivel Form

Cuando se selecciona un Form:

- aparecen sus fields indexados;
- operadores coherentes con Field Type;
- columnas configurables;
- filtros combinables.

Ejemplos:

- email contiene dominio;
- importe > 1000;
- fecha entre A y B;
- provincia = Madrid;
- consentimiento = sí.

## 6. Filtros combinados

Debe permitirse:

- AND;
- múltiples condiciones;
- presets/filtros guardados.

OR avanzado podrá planificarse si complica la primera release, pero el modelo de query no debe impedirlo.

## 7. Saved Views

El Administrator debería permitir guardar una vista:

- Form;
- filtros;
- columnas;
- order;
- nombre.

Puede ser privada por usuario o compartida si ACL lo permite.

## 8. Paginación a escala

Para tablas masivas se evitará depender únicamente de `OFFSET N` a profundidades enormes.

El SearchProvider debe poder implementar keyset/cursor pagination usando orden estable, por ejemplo:

`received_at DESC, id DESC`.

La UI puede seguir presentando navegación cómoda sin obligar a contar/offsetear millones constantemente.

## 9. Total counts

Un `COUNT(*)` exacto sobre filtros complejos puede ser costoso.

La interfaz debe distinguir:

- exact count cuando sea razonable;
- estimate/unknown cuando el provider lo determine;
- conteos preagregados futuros.

No bloquear una pantalla únicamente para obtener un total exacto decorativo.

## 10. Ordenación

Solo campos declarados sortable.

Nunca construir `ORDER BY` directamente desde input no validado.

## 11. Global search

El contrato SearchProvider permite:

### SQL Core Provider
Filtros estructurados e indexación relacional.

### Full-text DB provider opcional
Si se decide implementar por motor.

### External Search Provider futuro
Por ejemplo un motor dedicado.

El dominio no se acoplará a una marca concreta.

## 12. Reindexado

Debe poder:

- reindexar un Form;
- reindexar periodo;
- reindexar Submission;
- reconstruir índice completo.

Reindexar no modifica canonical payload.

Para grandes volúmenes se ejecuta como Job resumible.

## 13. Cambios de configuración de indexación

Si se marca un field antiguo como searchable:

- nuevas submissions se indexan inmediatamente;
- histórico queda `index pending`;
- un job backfill reconstruye el índice.

Administrator debe mostrar progreso/estado.

## 14. Exportaciones

Exportar el resultado de un filtro:

- no carga todo en memoria;
- procesa por chunks/cursor;
- genera file temporal/protegido;
- registra Job;
- permite descargar cuando finaliza;
- expira según política.

Formatos iniciales:

- CSV;
- JSON.

Futuros:

- XLSX;
- NDJSON;
- otros providers.

## 15. Millones de filas y acciones masivas

Una operación sobre "todo lo que coincide con este filtro" no enviará todos los IDs desde el browser.

Se persistirá:

- query/filtro inmutable;
- snapshot temporal o strategy;
- Job;
- progreso;
- errores.

## 16. Retención e índices

Eliminar/anonimizar canonical data debe actualizar/eliminar su search projection.

No pueden quedar PII buscable después de su eliminación lógica/física según política.

## 17. Vista de Submission

La pantalla de detalle puede reconstruir el layout desde FormVersion, pero no debe ejecutar Data Sources remotas históricas para interpretar un valor.

Labels/opciones necesarias deben estar snapshotizadas/resolubles históricamente.

## 18. Observabilidad

Métricas/health:

- total submissions por Form;
- index lag;
- failed index operations;
- export jobs;
- average search latency opcional;
- storage growth.

## 19. Índices de DB

Todo índice físico se justificará por patrones de query.

No se crearán índices indiscriminados en cada columna.

Se documentarán con query plans en pruebas de escala.

## 20. Degradación

Si SearchProvider externo no está disponible:

- no perder canonical submissions;
- registrar el fallo;
- marcar index pending;
- según arquitectura, permitir búsquedas core limitadas o informar indisponibilidad;
- reindexar al recuperar servicio.


---

<!-- SOURCE: 24_POST_SUBMIT_UX_SPEC.md -->

# 24 — Post-submit, mensajes y experiencia posterior


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Principio

Configurar un Form incluye configurar qué ocurre **después** de pulsar Enviar.

No debe requerir editar templates o PHP.

## 2. Categorías de resultado

- success;
- validation_error;
- captcha_error;
- anti_spam_rejected;
- rate_limited;
- upload_error;
- persistence_error;
- action_partial_failure;
- action_blocking_failure;
- form_unavailable;
- permission_error;
- session/csrf error;
- unexpected_error.

## 3. Mensajes configurables

Cada Form podrá personalizar, con fallback global:

- encabezado de éxito;
- cuerpo de éxito;
- mensaje de validación;
- mensaje CAPTCHA;
- mensaje de rate limit;
- mensaje upload;
- mensaje temporal/retry;
- mensaje indisponible;
- error genérico.

No mostrar detalles técnicos al visitante.

## 4. Comportamiento de éxito

Opciones:

- mantener Form y mostrar mensaje;
- ocultar Form y mostrar mensaje;
- resetear Form;
- conservar determinados valores;
- mostrar un summary de respuestas autorizado;
- redirigir a Menu Item;
- redirigir a URL autorizada;
- ir a página/estado de confirmación;
- entregar identificador/reference code.

## 5. Redirect

Configurable:

- Menu Item;
- internal route;
- approved URL.

Evitar open redirect: no redirigir a una URL enviada libremente por el visitante.

## 6. Mensaje condicional

El mensaje de success podrá seleccionarse condicionalmente.

Ejemplo:

- tipo soporte → mensaje A;
- tipo comercial → mensaje B.

Debe usar lógica declarativa.

## 7. Emails de notificación

Cero o varios.

Cada email:

- condition;
- to;
- cc;
- bcc;
- reply-to;
- subject;
- template/body;
- attachment rules;
- field inclusion;
- failure policy.

## 8. Autorespuesta

Un email al visitante puede configurarse como Action separada.

Debe:

- obtener destinatario de un Field Type email validado;
- permitir condition;
- tener subject/body;
- admitir tokens seguros;
- registrar ActionRun.

## 9. Respuesta al administrador

No confundir:

- notificación interna;
- autorespuesta al visitante.

Son Actions independientes.

## 10. Attachments

Se podrá decidir:

- adjuntar uploads;
- no adjuntar y proporcionar referencia segura;
- límites.

Por seguridad y tamaño, el default debería evitar adjuntar indiscriminadamente archivos grandes.

## 11. Partial Action Failure

Si Submission se guarda pero una Action non-blocking falla:

- al visitante se puede confirmar recepción;
- se registra el fallo;
- Administrator lo muestra;
- retry disponible.

El mensaje al usuario no debe afirmar que un email externo fue enviado si no lo fue, salvo que ese detalle sea irrelevante para la promesa comunicada.

## 12. Blocking Failure

Se debe especificar si:

- rollback es posible;
- Submission queda en error;
- se conserva para diagnóstico;
- el usuario puede retry sin duplicar.

La transacción exacta dependerá del tipo de Action; no se prometerá atomicidad distribuida imposible con servicios externos.

## 13. Form reset

Configurar:

- reset all;
- preserve selected fields;
- no reset.

En redirect no aplica salvo persistencia cliente explícita.

## 14. Reference code

Puede mostrarse un identificador seguro y no predecible de Submission para soporte.

No debe exponer un ID secuencial si eso crea riesgo.

## 15. Eventos post-submit

El motor expondrá eventos/hooks para integraciones, además de Actions declarativas.

## 16. UX AJAX

Durante submit:

- evitar doble click;
- indicar progreso;
- mantener accesibilidad;
- restaurar botón ante error;
- mover foco a resultado;
- tratar timeout sin asumir automáticamente que servidor no recibió el POST.

## 17. Confirmación antes de envío

Opción futura/compatible:

- página de revisión previa;
- checkbox de confirmación;
- summary.

El modelo multipaso deberá permitirlo.

## 18. Mensajes globales y overrides

Jerarquía:

1. Form override;
2. template/resource si aplica;
3. component global default;
4. language default.

## 19. Traducción

Todos los mensajes son traducibles.

## 20. Logging

La configuración de mensajes nunca debe provocar que el log guarde contenido personal innecesario.


---

<!-- SOURCE: 25_JOOMLA_CAPTCHA_ANTISPAM_SPEC.md -->

# 25 — Integración Joomla CAPTCHA y anti-spam


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## 1. Decisión

Nicode EasyForms **NO implementará un CAPTCHA propio**.

Utilizará la infraestructura CAPTCHA de Joomla y los proveedores/plugins CAPTCHA instalados y habilitados.

Esta decisión reduce:

- dependencia de un proveedor concreto;
- duplicación;
- mantenimiento criptográfico/anti-bot;
- configuración paralela.

## 2. Capacidades Joomla a utilizar

La integración debe poder:

- usar el CAPTCHA global por defecto de Joomla;
- seleccionar un plugin/proveedor CAPTCHA instalado cuando Joomla permita esa selección;
- desactivar CAPTCHA por Form si la política global/ACL lo permite;
- renderizar mediante la API/Field de CAPTCHA actual;
- validar mediante el provider Joomla actual.

## 3. Configuración global EasyForms

`Default CAPTCHA policy`:

- Joomla global default;
- specific available provider;
- none;
- required policy según instalación.

## 4. Configuración por Form

- inherit EasyForms default;
- Joomla default;
- specific installed provider;
- none si permitido.

Administrator debe listar providers disponibles, no una lista hardcoded.

## 5. Posición visual

Aunque CAPTCHA es política de seguridad del Form, el Builder debe permitir decidir su posición mediante un elemento de sistema, por ejemplo:

`System → CAPTCHA`

Restricciones:

- máximo definido por política;
- no duplicarlo accidentalmente;
- si no se coloca, Renderer puede usar posición predeterminada configurable.

## 6. Múltiples Forms en una página

Cada instancia deberá utilizar namespace/instance identity apropiado para evitar colisión entre CAPTCHA instances.

## 7. Publicación

Si un Form exige provider X y X no está instalado/habilitado:

- Compiler/Admin muestra ERROR;
- Form no debe publicarse o runtime debe fail closed si se deshabilita posteriormente;
- jamás omitir CAPTCHA silenciosamente.

## 8. Runtime

Orden recomendado:

- disponibilidad/access;
- session/CSRF;
- anti-abuse prechecks;
- CAPTCHA validation;
- payload completo/expensive work según necesidad.

El orden definitivo deberá evitar tanto bypass como trabajo innecesario.

## 9. Joomla y proveedores

EasyForms no debe asumir reCAPTCHA, hCaptcha, Turnstile u otra marca.

El provider lo decide el sitio Joomla.

## 10. Otros mecanismos anti-spam

CAPTCHA no reemplaza:

- CSRF;
- rate limiting;
- duplicate protection;
- input validation.

EasyForms podrá ofrecer controles complementarios como:

- timing;
- honeypot si se decide;
- rate limit;
- throttling;
- idempotency;

pero, cuando Joomla ofrezca una capacidad equivalente reutilizable, se preferirá integración con Joomla.

## 11. Honeypot

Si se incorpora un honeypot EasyForms, debe considerarse una heurística anti-spam, no un CAPTCHA.

También podría suministrarse por un provider CAPTCHA Joomla, por lo que no debe ser requisito obligatorio duplicarlo.

## 12. Configuración segura

No almacenar secretos de providers CAPTCHA dentro de FormSpec portable.

Los secretos pertenecen a la configuración del plugin/provider Joomla.

## 13. Error al visitante

CAPTCHA failure tendrá mensaje configurable/traducible.

No exponer detalles internos del provider.

## 14. Auditoría

No almacenar challenge tokens/secret responses salvo requisito técnico temporal y seguro.

## 15. Actualización futura

Joomla ha evolucionado su API CAPTCHA; EasyForms debe usar la API moderna disponible en Joomla 6 y encapsularla en un `CaptchaAdapter` interno para reducir acoplamiento.


---

<!-- SOURCE: 26_IMPORT_EXPORT_VERSIONING_SPEC.md -->

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


---

<!-- SOURCE: 27_OPERATIONS_OBSERVABILITY_SPEC.md -->

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


---

<!-- SOURCE: 28_DECISIONS_AND_NON_GOALS.md -->

# 28 — Decisiones y no-objetivos


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## Decisiones iniciales

### D-001 — Package
Component + Module + Library en un Joomla Package.

### D-002 — FormSpec
Runtime basado en snapshot publicado e inmutable.

### D-003 — Authoring separado
Builder relacional separado del runtime.

### D-004 — Hybrid submissions
Canonical payload + typed search projection.

### D-005 — Registries
Field Types, validators, Rules, Data Sources, Actions, Search y Storage extensibles.

### D-006 — Joomla CAPTCHA
No CAPTCHA propio.

### D-007 — Server authoritative
JS nunca autoridad.

### D-008 — No arbitrary code
Sin PHP/JS/SQL arbitrario.

### D-009 — Historical correctness
Submission siempre vinculada a FormVersion.

### D-010 — Massive operations as jobs
Export/reindex/retention masivos no dependen de una única request.

## No-objetivos iniciales

No se pretende en primera instancia:

- sustituir a un BI completo;
- ser un CRM;
- ser un workflow/BPM universal;
- ofrecer editor de código arbitrario;
- implementar CAPTCHA propio;
- implementar un motor de búsqueda externo propio;
- crear una tabla SQL por Form;
- garantizar atomicidad distribuida entre DB y sistemas externos;
- ser un constructor visual de páginas generalista.

Estas capacidades podrán integrarse mediante providers o Actions cuando tenga sentido.

## Decisiones pendientes para ADR

- DB compatibility matrix exacta de la primera release;
- formato exacto del canonical payload;
- estrategia de secrets;
- Job execution mechanism;
- search provider core;
- editor visual implementation;
- repeatable groups in v1;
- import format signature;
- retention defaults;
- upload storage default.


---

<!-- SOURCE: 29_REQUIREMENTS_TRACEABILITY.md -->

# 29 — Requirements Traceability


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


## FORM

- **FORM-001** Crear múltiples formularios sin límite artificial.
- **FORM-002** Editar Form.
- **FORM-003** Publicar/despublicar.
- **FORM-004** Archivar/papelera/eliminar.
- **FORM-005** Duplicar.
- **FORM-006** Versionar.
- **FORM-007** Preview.
- **FORM-008** Compile & Publish.
- **FORM-009** Import/export.
- **FORM-010** Idioma/access/publication window.

## FIELD

- **FIELD-001** Fields ilimitados artificialmente.
- **FIELD-002** Registry extensible.
- **FIELD-003** Tipos texto.
- **FIELD-004** Numéricos.
- **FIELD-005** Fechas/tiempo.
- **FIELD-006** Selecciones.
- **FIELD-007** Files.
- **FIELD-008** Consent.
- **FIELD-009** Presentation elements.
- **FIELD-010** Propiedades min/max/step/precision/etc.
- **FIELD-011** Prefill.
- **FIELD-012** Sensitive/index flags.
- **FIELD-013** Machine name estable.
- **FIELD-014** Multi-value/repeatability compatible.

## LAYOUT

- **LAYOUT-001** Group/section/fieldset.
- **LAYOUT-002** Row/columns.
- **LAYOUT-003** Responsive.
- **LAYOUT-004** Multipaso.
- **LAYOUT-005** Builder drag/drop + alternativa accesible.
- **LAYOUT-006** Repeatable groups compatible.

## RULE

- **RULE-001** Mostrar/ocultar Field.
- **RULE-002** Mostrar/ocultar Group.
- **RULE-003** Required/optional dinámico.
- **RULE-004** Enable/disable.
- **RULE-005** Set/clear value.
- **RULE-006** Cambiar/filter options.
- **RULE-007** AND/OR nested.
- **RULE-008** Prioridad determinista.
- **RULE-009** Cycle detection.
- **RULE-010** Client/server equivalence.

## DATA

- **DATA-001** Local options.
- **DATA-002** Option Sets.
- **DATA-003** Dependent options.
- **DATA-004** Data Source Registry.
- **DATA-005** Cache/TTL.
- **DATA-006** Revalidación server.

## SUB

- **SUB-001** Pipeline único.
- **SUB-002** AJAX/traditional.
- **SUB-003** Canonical persistence.
- **SUB-004** Store/no-store.
- **SUB-005** Idempotency/double-submit mitigation.
- **SUB-006** Historical FormVersion.
- **SUB-007** Files.
- **SUB-008** States.
- **SUB-009** Direct POST revalidation.
- **SUB-010** Millions of submissions support.

## SEARCH

- **SEARCH-001** Search projection typed.
- **SEARCH-002** Filters by Form.
- **SEARCH-003** Filters by indexed Field.
- **SEARCH-004** Large dataset pagination/cursor.
- **SEARCH-005** Saved views.
- **SEARCH-006** SearchProvider abstraction.
- **SEARCH-007** Reindex.
- **SEARCH-008** Export by filter.
- **SEARCH-009** Massive operations as jobs.
- **SEARCH-010** Sensitive index policy.

## ACTION

- **ACTION-001** Multiple actions.
- **ACTION-002** Conditional Actions.
- **ACTION-003** Notification email.
- **ACTION-004** Autoresponse.
- **ACTION-005** Redirect.
- **ACTION-006** Webhook.
- **ACTION-007** Blocking/non-blocking.
- **ACTION-008** ActionRun.
- **ACTION-009** Retry.
- **ACTION-010** Tokenized templates.

## POST

- **POST-001** Configurable success message.
- **POST-002** Configurable error categories.
- **POST-003** Hide/reset/preserve Form.
- **POST-004** Conditional success.
- **POST-005** Redirect to Menu Item/approved URL.
- **POST-006** Reference code.
- **POST-007** Accessible AJAX state.

## FRONT

- **FRONT-001** Menu Item Type.
- **FRONT-002** Dynamic Form selector.
- **FRONT-003** Single generic module.
- **FRONT-004** Shared runtime.
- **FRONT-005** Multiple instances.
- **FRONT-006** Assets only when needed.

## CAPTCHA/SEC

- **SEC-001** Joomla CSRF.
- **SEC-002** Joomla CAPTCHA provider integration.
- **SEC-003** No custom CAPTCHA algorithm.
- **SEC-004** Provider availability validation.
- **SEC-005** Server validation.
- **SEC-006** ACL checks.
- **SEC-007** Safe uploads.
- **SEC-008** Parameterized SQL.
- **SEC-009** Contextual escaping.
- **SEC-010** Header injection prevention.
- **SEC-011** SSRF controls.
- **SEC-012** Rate limiting strategy.
- **SEC-013** CSV injection prevention.

## PRIVACY

- **PRIV-001** Sensitive Field.
- **PRIV-002** Retention.
- **PRIV-003** Anonymize.
- **PRIV-004** Delete.
- **PRIV-005** Consent version.
- **PRIV-006** IP/User-Agent opt-in policy.
- **PRIV-007** ACL sensitive.

## ADMIN

- **ADMIN-001** Dashboard.
- **ADMIN-002** Forms list/filter.
- **ADMIN-003** Builder.
- **ADMIN-004** Logic editor.
- **ADMIN-005** Actions editor.
- **ADMIN-006** Submissions explorer.
- **ADMIN-007** Detail.
- **ADMIN-008** Massive operations.
- **ADMIN-009** Logs.
- **ADMIN-010** System info.

## OPS

- **OPS-001** Package install.
- **OPS-002** Schema migrations.
- **OPS-003** Data-preserving uninstall option.
- **OPS-004** Jobs.
- **OPS-005** Audit.
- **OPS-006** Logs.
- **OPS-007** Health.
- **OPS-008** Reproducible release.

## UX/A11Y/I18N

- **A11Y-001** WCAG 2.2 AA target.
- **I18N-001** Joomla language files.
- **I18N-002** Dynamic translations.
- **UX-001** Preview same renderer.
- **UX-002** Errors linked to fields.
- **UX-003** Responsive layout.


---

<!-- SOURCE: 99_REFERENCES.md -->

# 99 — Referencias técnicas


> Proyecto: **Nicode EasyForms**  
> Estado del documento: **Especificación inicial normativa**  
> Plataforma objetivo: **Joomla 6.x**  
> Principios obligatorios: **SPEC-DRIVEN** y **DATA-DRIVEN**
>
> Convenciones: **MUST/DEBE** = requisito obligatorio; **SHOULD/DEBERÍA** = recomendado salvo causa documentada; **MAY/PUEDE** = opcional.  
> Todo cambio funcional deberá modificar primero o simultáneamente la especificación correspondiente y sus criterios de aceptación.


Fecha de revisión inicial de referencias: **2026-09-26**.

Estas referencias documentan capacidades de Joomla utilizadas por la arquitectura. La especificación del producto no debe copiar ciegamente ejemplos antiguos; antes de implementar se comprobará la documentación de la versión objetivo exacta.

## Joomla Programmer Documentation

### Extensions y Packages
- https://manual.joomla.org/docs/next/building-extensions/
- https://manual.joomla.org/docs/5.4/building-extensions/packages/
- https://manual.joomla.org/docs/next/building-extensions/install-update/installation/

### MVC
- https://manual.joomla.org/docs/next/building-extensions/components/mvc/
- https://manual.joomla.org/docs/next/building-extensions/components/mvc/library-mvc/

### Administrator lists, filtros y paginación
- https://manual.joomla.org/docs/next/building-extensions/components/component-development-tutorial/step06-admin-list/
- https://manual.joomla.org/docs/next/building-extensions/components/component-development-tutorial/step12-filter-form/

### CAPTCHA
- https://manual.joomla.org/docs/next/general-concepts/forms-fields/standard-fields/captcha/
- https://manual.joomla.org/docs/next/general-concepts/forms-fields/standard-fields/plugins/
- https://manual.joomla.org/docs/next/building-extensions/plugins/plugin-examples/captcha-plugin/

La documentación actual indica que el Form Field CAPTCHA accede a un plugin CAPTCHA instalado y que los providers se registran mediante la infraestructura CAPTCHA de Joomla.

### ACL
- https://manual.joomla.org/docs/next/general-concepts/acl/acl-permissions/

### Web Asset Manager
- https://manual.joomla.org/docs/next/general-concepts/web-asset-manager

### CLI plugins
- https://manual.joomla.org/docs/5.4/building-extensions/plugins/plugin-examples/basic-console-plugin-helloworld/

### Requisitos técnicos Joomla 6.x
- https://manual.joomla.org/docs/next/get-started/technical-requirements/

En la revisión de 2026-09-26 la documentación de Joomla 6.x enumera PHP 8.3 como versión soportada mínima y MySQL, MariaDB y PostgreSQL entre las bases de datos soportadas. La matriz exacta de Nicode EasyForms se fijará independientemente y se probará.

### Cambios/deprecations CAPTCHA
- https://manual.joomla.org/updates/53-54/changed-deprecations/
- https://manual.joomla.org/updates/44-50/removed-backward-incompatibility/

## Principio de uso de referencias

Antes de implementar una API:

1. verificar documentación de la versión Joomla objetivo;
2. verificar deprecations;
3. preferir API moderna;
4. encapsular integraciones susceptibles de evolución;
5. añadir test de integración.
