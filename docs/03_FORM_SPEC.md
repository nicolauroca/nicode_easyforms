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
