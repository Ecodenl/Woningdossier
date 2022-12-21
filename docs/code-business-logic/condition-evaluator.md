# The Condition Evaluator
The [ConditionEvaluator](./../../app/Helpers/Conditions/ConditionEvaluator.php) is a Helper class that wraps 
tool question answer evaluation neatly.

## How it works
The evaluator works by providing conditions, and using a building and input source to return a boolean, which is 
`true` if the conditions pass. These conditions can be built on the fly, but can also be predefined from a model.

#### Models that support conditions:
- [Alert](./../../app/Models/Alert.php)
- [SubStep](./../../app/Models/SubStep.php)
- [SubSteppable](./../../app/Models/SubSteppable.php)
- [ToolQuestionCustomValue](./../../app/Models/ToolQuestionCustomValue.php)
- [ToolQuestionValuable](./../../app/Models/ToolQuestionValuable.php)

These models have a `conditions` column that needs to be evaluated before these can be shown.

Example of evaluation:
```php 
ConditionEvaluator::init()
    ->building($building)
    ->inputSource($masterInputSource)
    ->evaluate($subStep->conditions ?? []);
```

Sometimes, answers can be dynamic. We use Livewire, in which the answers are changed but not updated in the
database. There we offer `evaluateCollection`:

```php 
ConditionEvaluator::init()
    ->building($building)
    ->inputSource($masterInputSource)
    ->evaluateCollection($toolQuestion->conditions, $evaluatableAnswers)
```

This evaluates in the same manner, but you'll need to provide the answers yourself. In the case you have some 
answers but not all, you could also merge them:

```php 
$evaluator = ConditionEvaluator::init()
    ->building($this->building)
    ->inputSource($this->inputSource);
  
$answers = $evaluator->getToolAnswersForConditions($toolQuestion->conditions)->merge(collect($answers));

$evaluation = $evaluator->evaluateCollection($toolQuestion->conditions, $answers);
```

## Constructing conditions
To be able to understand how to use the evaluator further, you need to be able to know how conditions are built.

### Step 1: Structure
A structure is defined in 3 groups (all of which are `arrays`):

- A singular outer group, which holds all the condition clauses.
- An undefined amount of OR clauses. Only a single clause inside this group has to be truthy.
- An undefined amount of AND clauses. Every clause inside this group must be truthy.
  - An AND clause can hold a subgroup of OR clauses, to allow OR statements to be combined with AND statements.

Example:
```php
// Outer holding group
[
    // OR groups
    [
        // AND groups
        [
            'column' => 'heat-source',
            'operator' => Clause::CONTAINS,
            'value' => 'heat-pump',
        ],   
        [
            'column' => 'new-heat-source',
            'operator' => Clause::CONTAINS,
            'value' => 'heat-pump',
        ],
    ],
    [
        [
            [
                'column' => 'new-heat-source',
                'operator' => Clause::CONTAINS,
                'value' => 'heat-pump',
            ],
            [
                'column' => 'new-heat-source-warm-tap-water',
                'operator' => Clause::CONTAINS,
                'value' => 'heat-pump',
            ],
        ],
        [
            'column' => 'heat-pump-replace',
            'operator' => Clause::EQ,
            'value' => true,
        ],
    ],
];
```

This example will pass if both the question `heat-source` and `new-heat-source` have the value `heat-pump`, or if 
either `new-heat-source` or `new-heat-source-warm-tap-water` holds `heat-pump`, and `heat-pump-replace` is `true`.

### Step 2: Final clause structure
To be able to evaluate something, there needs to be certain logic to process these conditions. Therefore, a clause 
requires to be built in a specific manner.

#### Normal use case
In the normal case, we expect the following values inside the clause:

- `value`: Holds the value that the answer needs to be.
- `column`: Holds the short of the tool question.
- `operator`: Holds the operator for evaluation.
  - `=`: Value must be equal to answer
  - `!=`: Value must not be equal to answer
  - `>`: Value must be greater than answer
  - `>=`: Value must be equal or greater than answer
  - `<`: Value must be smaller than answer
  - `<=`: Value must be equal or smaller than answer
  - `contains`: If answer is multi, one of the answers must be value. Otherwise, value must be equal to answer.
  - `not-contains`: If answer is multi, none of the answers must be value. Otherwise, value must not be equal to answer.
  - `passes`: This clause is _slightly_ different. The value should hold the `class` of the model to check. The 
    evaluator will then evaluate the conditions for the model. If column is an `array`, you can define a 
    `column => value` structure for querying. Otherwise, the `column` will default to `short`. This is supported because 
    for example `ToolQuestionCustomValues` are linked to a `ToolQuestion` so finding by short might give wrong 
    results, and models such as `SubSteps` don't have a short column. 
  - `not-passes`: Negative of `passes`. 

#### Special use case
Sometimes evaluation is a little different, where the default evaluation doesn't cut it. Special use cases exist, and 
that's why there's custom evaluators. They can be found [here](./../../app/Helpers/Conditions/Evaluators/). In this
case, the columns are as follows:

- `value`: A potential value to use in the custom evaluator
- `column`: `fn`. `fn` indicates the evaluator to use a custom evaluator.
- `operator`: The name of the custom evaluator.

Just like the main evaluator, a building and input source are passed to these evaluators. However, these evaluators 
also accept a nullable value, as well as a `Collection` of answers, to allow for dynamic and complex logic.

## Developer notes
### Custom evaluator "override"
The custom evaluators share their results with the condition evaluator so any next checks of the same custom evaluator 
will allow the earlier result to be checked. This saves a ton of unnecessary duplicate processes. The custom evaluator 
will return an `array` containing `results`, which are the results of the evaluation, `bool` which is whether the
evaluation has passed and `key` which is an MD5 to differentiate between custom evaluators with different parameters.

### Easy retrieval of many conditions
Since we often need all conditions in one go, we can easily get them using the following syntax:

#### Model with conditions column
```php
// Pluck conditions, flatten to construct proper depth of array, remove `null` values, then convert to array. 
// Depth is 1 as these are all conditions from all models in the collection one by one, while really
// we want them as one large collection. Flatten with 1 will do exactly that. 
$modelCollection->pluck('conditions')->flatten(1)->filter()->all();
```

#### Model with conditions in relation
```php
// ENSURE RELATION IS FULLY EAGER LOADED!
// Pluck conditions from relation, flatten to construct proper depth of array, remove `null` values, then convert to
// array.
// Depth is 2, because due to the wildcard, we get an extra nest.
$modelCollection->pluck('relation.*.conditions')->flatten(2)->filter()->all();
```

#### Model with conditions and relation with conditions
```php
// ENSURE RELATION IS FULLY EAGER LOADED!
// We combine the logic of above 2. Because of this, we only need to flatten the merged collection once, due to it 
// then being the same depth as the "main" collection, and we need to flatten that also, so we can combine it. 
$modelCollection->pluck('conditions')
    ->merge($modelCollection->pluck('relation.*.conditions')->flatten(1))
    ->filter()
    ->flatten(1)
    ->all();
```

In case the result conditions should have normal indexed keys, it's important to call `->values()` before `->all()`, 
otherwise you might get a result array with associative keys.