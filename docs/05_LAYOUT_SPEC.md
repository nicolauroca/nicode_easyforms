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
