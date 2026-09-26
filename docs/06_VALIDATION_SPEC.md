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
