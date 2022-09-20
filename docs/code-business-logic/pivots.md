# Pivots
A pivot is a so called "in between" table that connects many of one model to many of other models. A simple exaple 
in our case is with SubSteps and ToolQuestions. A SubStep can have many ToolQuestions, and a ToolQuestion can have 
many SubSteps. We use the SubSteppable pivot to create a connection.

## Issues
### Serializing
Pivots aren't exactly relations. There's some magic in Laravel that creates the connection from A to B, and this 
gives us some pivot values when the model is loaded. However, when the model is serialized and reloaded, it isn't 
providing us with the pivot values again. However, there's a workaround. By eager loading the relation, the pivot is 
kept after serializing.

Example:
```php 
$subStep->load('toolQuestions');
// Calling this each render will properly fetch the pivot again.
$this->toolQuestions = $subStep->toolQuestions; 
```