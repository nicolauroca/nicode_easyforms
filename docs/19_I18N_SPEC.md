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
