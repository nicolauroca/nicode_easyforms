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
