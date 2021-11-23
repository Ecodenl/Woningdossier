# Decisions
#### This file will contain important decisions being made throughout the development of Hoomdossier. This will mainly contain decisions made by Hoom itself. 

## CSV Total report.
The total-report contains all the data from a cooperation. On 12-11-2021 we decided to only show master input source data. Previously we would show the coach or resident data based on what was available.

## MapActionPlan

#### Costs to JSON
We initially decided that we would set every value to the 'from' option within the JSON array.
However, that made no sense, because if a value is higher than 0, it isn't logical to put it in 
a 'from' if a 'to' isn't set. Therefore, if the value is higher than 0, it will be set in the 
'to' option. This also applies to how formatting is done within the application as well.
It will only be saved to the 'from' option if the value is less than or equal to 0.

#### Master input source 
The mapping for `user_action_plan_advices` also includes mapping to the master input source.
The logic is simple: We check if there are coach rows. If so, we take those. Else we take
the resident. This is because the `user_action_plan_advices` are too complex to map
via the `AddMasterInputSource` command.