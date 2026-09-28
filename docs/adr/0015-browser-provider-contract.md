# ADR 0015 — Providers en el navegador

Estado: aceptado.

Los registries PHP permiten extensiones, pero el navegador no puede sustituir
su normalización o reglas por aproximaciones genéricas. Los providers de campos,
operadores y efectos personalizados deben implementar `BrowserProviderInterface`.
Los validators pueden implementarlo o seguir siendo explícitamente server-only.

El contrato declara un nombre de script registrado en Joomla Web Asset Manager y
una lista de claves de configuración públicas. El runtime resuelve ese asset;
ninguna URL o código procede de la definición editable. El módulo ES exporta
`providers`, organizado por tipo de registry e ID. Cada entrada declara `version`
y funciones síncronas. La versión debe coincidir exactamente con el provider PHP
instalado; la compatibilidad de este con la publicación sigue el contrato existente.

`styles` permite declarar nombres de assets CSS de WAM. Se cargan una vez por
documento, incluyendo el iframe de preview. Las dependencias JavaScript de un
módulo se expresan mediante imports ES; no se infiere un orden entre scripts
globales. Módulos y estilos tienen un límite de carga de 15 segundos; un error o
timeout bloquea las instancias dependientes y muestra el mensaje de indisponibilidad.

El navegador importa los módulos antes de construir cada instancia. Importaciones
compartidas se reutilizan, pero un fallo bloquea solo las instancias dependientes.
No se permite sustituir providers core. La previsualización usa el mismo loader.
El servidor sigue siendo autoritativo, incluso si el usuario altera JavaScript.

Hooks: fields: `read(controls, field)`, `normalize(raw, config, datatype)`,
`validate(value, config, datatype, state)` (códigos de error),
`update(controls, field, state)`; operators: `evaluate(left,right,datatype)`;
effects: `apply(state, configuration)` (nuevo estado del target);
validators: `validate(values, configuration, datatypes)` (mapa UUID→códigos).
Los hooks de datos reciben copias para impedir mutaciones entre instancias.
Los widgets deben conservar los nombres HTML nativos y emitir input/change.
Los módulos son código de plugins instalados y tienen la misma confianza que
estos; no son un sandbox para código aportado por autores de formularios.

Un operador externo puede declarar `datatypes` en su metadata para admitir tipos
lógicos existentes sin modificar sus providers. El compiler y el selector de
reglas suman esa compatibilidad a los operadores declarados por el campo. Esta
ampliación no reemplaza ni amplía operadores core.

Un operador externo puede declarar `datatypes` en su metadata para admitir tipos
lógicos existentes sin modificar sus providers. El compiler y el selector de
reglas suman esa compatibilidad a los operadores declarados por el campo. Esta
ampliación no reemplaza ni amplía operadores core.

La configuración permanece privada por defecto. Campos conservan las propiedades
core públicas; extensiones añaden solo claves declaradas. Efectos añaden esas claves
al contrato target/type/value; validators solo reciben sus claves declaradas.
Operadores usan exclusivamente los tres operandos del contrato PHP.
