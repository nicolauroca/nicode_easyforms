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
