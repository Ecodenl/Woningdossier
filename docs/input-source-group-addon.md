#Input source group addon
#### Filename:  input-group.blade.php

`$inputType:` The type of a input, options: `select, input, checkbox, radio` 

`$inputValues:` The values for the dropdown or checkbox

`$userInputValues:` **All** the answers for the `$inputValues` (including the one from different input sources) 
usage: `$building->relatedModel()->forMe()->get()` or `RelatedModel::forMe()->get()`.

`$userInputModel:` this accepts a relationship / model name

`$userInputColumn:` The column where the answer is stored.

`$customInputValueColumn:` 9 out of 10 times the "name" from the `$inputValues` will come from the `value` or 
`name` property, but if it does not we can pass it through

`$needsFormat:` if set, the value for the input will be formatted to 1 decimal.



### `$inputType` select
**This expects**\
`$inputValues`: collection or array \
`$userInputValues`: collection\
`$userInputColumn`: dotted array or property

**Optionally** \
`$userInputModel`: name from the related model or relationship name\
`$customInputValueColumn` 

This accepts a `array` and a Laravel `Collection` on the `$inputValues`, when a array is passed and the key is a int it expects a dotted 
`$userInputColumn` to access the array. The key of the array will get checked against the `$userInputColumn` value.

When a `Collection` is passed the `$userInputColumn` can be a property or a dotted array, this value will be checked against
the `$inputValue->id`. Optionally a `$userInputModel` can be passed, when this is set the the value comes from the
`$userInputColumn` on the `$userInputModel`. example: `$userInputValue->$userInputModel->$userInputColumn`.

### `$inputType` input
**This expects**\
`$userInputValues`: collection\
`$userInputColumn`: dotted array or property\
**Optionally** \
`$needsFormat`: if set, the value will be formatted with 1 decimal   

This accepts a Laravel `Collection` on the `$userInputValues`, the `$userInputColumn` can be a property or a dotted array
that needs to be accessed on the `$userInputValue`.
  

### `$inputType` checkbox
**This expects**\
`$inputValues`: collection or array \
`$userInputValues`: collection\
`$userInputColumn`: dotted array or property

this needs improvement currently only used in one place, due to the way of retrieving data in view / controller.

### `$inputType` radio
**This expects**\
`$inputValues`: array \
`$userInputValues`: collection\
`$userInputColumn`: dotted array or property

this needs improvement currently only used in one place, due to the way of retrieving data in view / controller.

