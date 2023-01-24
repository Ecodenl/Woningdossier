# Decisions
#### This file will contain important decisions being made throughout the development of Hoomdossier. This will mainly contain decisions made by Hoom itself. 

## Internal logic
### Answer structure
During the `upgrade:heat-pump`, we came across a question that had conditions based on a question which had conditions 
based on _another_ question. This made us wonder whether or not we should reset answers, or have calculations and 
conditions consider earlier set answers. During the `upgrade:quick-scan` we had a similar situation, with the 
`hr-boiler`. We then also applied the same logic as we are doing now. Any future condition/calculations should 
**always** check the answers for any related conditional answers.

## Formatting
### Costs JSON formatting
If a value is higher than 0, it isn't logical to put it in a 'from' if a 'to' isn't set. Therefore, if the value is
higher than 0, it will be set in the 'to' option. This also applies to how formatting is done within the application 
as well. It will only be saved to the 'from' option if the value is less than or equal to 0.
The only exception is, of course, if both 'from' and 'to' are set. Then, it's a valid
range and 'from' should contain the smallest value.

### Number formatting
It was decided that numbers should always be formatted as "1,0" within the quick scan,
unless it is a Slider number, or a number that should logically not be formatted,
like years.

## Scans
To prepare the tool for the future, we have converted the expert tool and quick scan to so called "scans". These are 
variants of the tool that make it easier to get certain info. For example, the quick scan is the baseline to get a 
quick overview, whereas the expert scan goes more in depth. 

### Dynamic steps
In the expert scan, we now have static and dynamic steps. The difference is that static scans are hardcoded, while 
dynamic scans are generated like the quick scan. 

### Expert scan "SubSteppable"
In the expert scan, we have a component for each sub step. This is called the "SubSteppable". They were separated 
from the main scan form component to be easier to maintain and not create a massive, confusing component. However, 
this has its downsides. This means that the main form and the unknown amount of sub step components don't have any 
direct communication. To overcome this, we use emits to create a bridge. The answers are transmitted to the main 
component, each time they are updated. This way, the main component knows _exactly_ what is going on. This component
will then perform the calculations, and return the results, so each SubSteppable can display them as required. 

## Duplicating user data for input source
This is a feature which "allows" the user to copy data from a opposing input source to its own.
It will be checked in the DuplicateDataForUser middleware.

When the current user has no data it will copy data from the opposing input source, this applies for a coach or resident.
The opposing input source could be anything, but mostly the master is used. 

## Jobs
### ApplyExampleBuildingForChanges
This job applies a example building with its content for the given changes, a build year or building type can be passed. When passed, these will be used to determine the building type.

In odd cases a build year can be `null`, if that happens we have a fallback.
- get a build year from whatever input source, as this is the most reliable we can get
- set the current year as build year so the code doesn't fail. 

## Reports
### CSV Total report.
The total-report contains all the data from a cooperation. On 12-11-2021 we decided to only show master input source data. Previously we would show the coach or resident data based on what was available.

### PDF Report
#### Input source
As per consistency, the decision was made to always render the PDF from the master
input source.

# Old decisions
These are decisions made in the past that are now not directly relevant any more (as perhaps the code has changed) 
but we keep them here for tracking.

## MapActionPlan
### Costs to JSON
We initially decided that we would set every value to the 'from' option within the JSON array.
However, that made no sense, because if a value is higher than 0, it isn't logical to put it in 
a 'from' if a 'to' isn't set. Therefore, we apply the formatting mentioned [above](#costs-json-formatting).

### Master input source 
The mapping for `user_action_plan_advices` also includes mapping to the master input source.
The logic is simple: We check if there are coach rows. If so, we take those. Else we take
the resident. This is because the `user_action_plan_advices` are too complex to map
via the `AddMasterInputSource` command.

## Measure applications
### Cooperation measure applications
As of the Lite scan, we got so called "extensive" measures, such as heat pump or floor insulation.
This is basically what happens in the Quick scan, but since the Lite scan can't answer those questions, they are 
added as such. In the code we refer to these measures as "extensive". The old, non-extensive measures are referred 
to as "small", but technically that's not correct. They are actually the cooperation's "own" measures, however in 
the code we use "small" as that makes more sense, as to being the opposite of "extensive".
