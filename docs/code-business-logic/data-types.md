# Data types
Tool Questions have a data type which is used to structure as well as format the answers for that tool question. To 
make the use of these data types easy, we have a [Caster Helper](./../../app/Helpers/DataTypes/Caster.php) which 
accepts a data type and value and returns the value casted to the expected data type. The strict casting is usually not
necessary, as the database is mostly strict typed anyway, however in the case you want the answer strictly typed, 
using the Caster is your best option.

## Supported types
- `string`
- `int`
- `float`
- `bool`
- `array`* 
- `json`: Converts JSON to an associative `array`
- `identifier`: Won't cast to anything. The answer is based on a relational value.

*In the case of a multi-answer, simply casting an answer as `array` won't properly give you all the available answers.