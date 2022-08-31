# The Condition Evaluator
The [ConditionEvaluator](./../../app/Helpers/Conditions/ConditionEvaluator.php) is a Helper class that wraps 
tool question answer evaluation neatly.

## How it works
The evaluator works by providing conditions, and using a building and input source to return a boolean, which is 
`true` if the conditions pass. These conditions can be built on the fly, but can also be predefined from a model.

#### Models that support conditions:
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
ConditionEvaluator::init()->evaluateCollection($toolQuestion->conditions, $evaluatableAnswers)
```

This evaluates in the same manner, but you'll need to provide the answers yourself.

## Constructing conditions
To be able to understand how to use the evaluator further, you need to be able to know how conditions are built.

### Step 1: Structure
A structure is defined in 3 groups (all of which are `arrays`):

- A singular outer group, which holds all the condition clauses.
- An undefined amount of OR clauses. Only a single clause inside this group has to be truthy.
- An undefined amount of AND clauses. Every clause inside this group must be truthy.

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

#### Special use case
Sometimes evaluation is a little different, where the default evaluation doesn't cut it. Special use cases exist, and 
that's why there's custom evaluators. They can be found [here](./../../app/Helpers/Conditions/Evaluators/). In this
case, the columns are as follows:

- `value`: The name of the custom evaluator.
- `column`: `fn`. `fn` indicates the evaluator to use a custom evaluator.

Just like the main evaluator, a building and input source are passed to these evaluators.