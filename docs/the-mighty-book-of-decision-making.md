# Decisions
#### This file will contain important decisions being made throughout the development of Hoomdossier. This will mainly contain decisions made by Hoom itself. 

## Duplicating user data for input source
This is a feature which "allows" the user to copy data from a opposing input source to its own.
It will be checked in the DuplicateDataForUser middleware.

When the current user has no data it will copy data from the opposing input source, this applies for a coach or resident.
The opposing input source could be anything, but mostly the master is used. 

##ApplyExampleBuildingForChanges
This job applies a example building with its content for the given changes, a build year or building type can be passed. When passed, these will be used to determine the building type.

In odd cases a build year can be null, if that happens we have a fallback.
- get a build year from whatever input source, as this is the most reliable we can get
- set the current year as build year so the code doesnt fail. 

## CSV Total report.
The total-report contains all the data from a cooperation. On 12-11-2021 we decided to only show master input source data. Previously we would show the coach or resident data based on what was available.

## PDF Report

#### Input source
As per consistency, the decision was made to always render the PDF from the master
input source.

## MapActionPlan

#### Costs to JSON
We initially decided that we would set every value to the 'from' option within the JSON array.
However, that made no sense, because if a value is higher than 0, it isn't logical to put it in 
a 'from' if a 'to' isn't set. Therefore, if the value is higher than 0, it will be set in the 
'to' option. This also applies to how formatting is done within the application as well.
It will only be saved to the 'from' option if the value is less than or equal to 0.
The only exception is, of course, if both 'from' and 'to' are set. Then, it's a valid
range and 'from' should contain the smallest value.

#### Number formatting
It was decided that numbers should always be formatted as "1,0" within the Quickscan,
unless it is a Slider number, or a number that should logically not be formatted,
like years.

