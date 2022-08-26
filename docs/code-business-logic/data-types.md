# Data types
Tool Questions have a data type which is used to structure as well as format the answers for that tool question. To 
make the use of these data types easy, we have a [Caster Helper](./../../app/Helpers/DataTypes/Caster.php) which 
accepts a data type and value and returns the value casted to the expected data type. 


## Supported types
- `string`
- `int`
- `float`
- `bool`
- `array`
- `json`: Converts JSON to an associative `array`
- `identifier`: Is cast to an `int`, but makes it clear that it's a foreign to another model.