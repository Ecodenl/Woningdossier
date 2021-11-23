# Decisions
#### This file will contain important decisions being made throughout the development of Hoomdossier. This will mainly contain decisions made by Hoom itself. 

## CSV Total report.
The total-report contains all the data from a cooperation. On 12-11-2021 we decided to only show master input source data. Previously we would show the coach or resident data based on what was available.

## MapActionPlan

#### Master input source 
The mapping for `user_action_plan_advices` also includes mapping to the master input source.
The logic is simple: We check if there are coach rows. If so, we take those. Else we take
the resident. This is because the `user_action_plan_advices` are too complex to map
via the `AddMasterInputSource` command.