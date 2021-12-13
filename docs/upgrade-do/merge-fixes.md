# After merge fixes

After the merge, quite a lot of data has bogged itself. This is 
a rundown of fixes to be ran on the data to try and correct it
as much as possible.

## Order of commands

Execute these commands/steps in this order to prevent data messing up:

```bash
php artisan merge:fix-completed-sub-steps
php artisan merge:fix-building-category
php artisan merge:fix-roof-type-valuables
php artisan merge:fix-cooperation-measures

- EXPORT ALL **EXPERT** STEPS FROM THE CURRENT LIVE ENVIRONMENT (1 TO 10) (`completed_steps`)
- TRUNCATE `completed_steps` && `completed_sub_steps`
- IMPORT OLD `completed_steps`
- DELETE ALL **EXPERT** STEPS FROM THE IMPORTED DATA (`completed_steps`)
- IMPORT ALL **EXPERT** STEPS EXPORTED EARLIER

php artisan merge:final-integrity-fix
```

## Explanation
### FixCompletedSubSteps

It fixes the `sub_steps` ID 33 to 5, because we migrated it from Woonplandossier,
it wasn't correct.

### FixBuildingCategory

The building category is a "relatively" new question. Because of 
this, not every user has this question answered. This command
gets every `building_features` that have a `building_type_id`,
but not a `tool_question_answers` for the building category question.

We do this because it is a mandatory question, and we don't want the user
to fall back to this even if they have a valid answer.

### FixRoofTypeValuables

During initial setup, we had a roof type by ID 4, for flat / pitched.
We removed this later as this was not applicable. At the time, Deltawind was
deployed and therefore had the old roof types.

During the migration, the new roof types were saved (ID 1 to 6), 
but the old valuables seemed to have made its way (ID 1 to 7, missing 4). 
This command fixes the ID.

If we don't do this, users that want to answer the roof type question get
an exception.

### FixCooperationMeasures

Our project manager wanted us to take all `cooperation_measure_applications`
from the testing environment. Turns out, one cooperation added one
of his own. The `user_action_plan_advices` were not merged for this.

This re-adds the `cooperation_measure_applications` for Duec, and converts
those pesky advices to the correct advisable.

We don't want a user to potentially think he is missing a very important card
on his action plan!

### FinalIntegrityFix

Because of a lot of messed up data, we do a similar thing to the `MapAnswers`;
We delete all `completed_steps` & `completed_sub_steps` and re-add them.
All existing users keep their old status. New users will follow the new
logic.
