#Input source group addon
#### Filename:  input-group.blade.php
`$inputValues:` The values for the dropdown or checkbox
`$userInputValues:` **All** the answers for the `$inputValues` (including the one from different input sources)
`$userInputModel:` this accepts a relationship / model name
`$customInputValueColumn:` 9 out of 10 times the "name" from the `$inputValues` will come from the values or name property
but if it does not we can give up the custom property name.
`$userInputColumn:` The column where the answer is stored.



A component blade file to render a input-group-btn with answers from different input sources.

##### case: select
This accepts `a array and a Laravel collection.`

 
      