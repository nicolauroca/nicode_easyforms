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
