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
