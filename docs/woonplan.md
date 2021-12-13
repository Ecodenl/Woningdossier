# Woonplan

The "woonplan", also referred to as "my plan" or "action plan" is the final screen
that describes your home and gives advices on how it could potentially be improved, whether
that'd be with improving its renewability or lowering your monthly costs.

### Usage

The woonplan is a simple drag-and-drop tool, that allows you to move cards to separate columns
based on your own preferences. They can be ordered as well, to your own taste. Advices
that you do not want to apply to your home can be dragged to the trash can, and then won't be
applied in calculating savings or investments. Any trashed advices can be added back to the
woonplan using the purple plus towards the right of the trash can. Simply clicking the removed
card will add it back to where it was before you trashed it.

You can also add custom advices using one of the 3 purple pluses at the top of each column. 
These can be more specific advices to your situation.

This way, the woonplan is a tool that allows you to create a personal plan on how to improve your home.

### Logic

#### How does the Woonplan work?

The woonplan lists the advices (cards) that Hoomdossier gives you in different categories (complete, 
to-do and later). The advices on the woonplan will be generated for the first time after completing the 
Quickscan, using the user filled answers, as well as the more detailed info provided by an example building. 

The user can change answers in both the Quickscan and the Expert mode to improve the quality of the woonplan
in relation to their own home. When making a change, the advices that are relevant to the given changes will 
be fully recalculated. This means that besides investment/cost values, their category and visibility will 
also be recalculated. The advices that have no changes (from updated questions) will not be re-categorized.

An example: Say a user has filled in that his wall insulation is good, the calculations will categorize 
the advice to `complete`. If a coach then notices there is no insulation at all, and changes this question,
the advice will be re-categorized to `to-do`, to reflect the new made change. Another advice, say glass 
insulation, which was positioned in category `complete` by the user, will still be in the category `complete`.

#### Mapping

Categories and visibility are calculated for each advice separately. Visibility has a relatively 
easy mapping; Always visible, except for maintenance measures. Maintenance is only visible if 
the user has answered an expert question, and for roof maintenance measures, they are only visible if the
measure year is within 5 years. 

Category mapping is more complex and is on a measure-basis. The most measures are mapped as being "if you 
have it, it's `complete`, else `to-do`". An example is insulation; insulation is `complete` if the user
has any insulation, and `to-do` if the user has no or unknown insulation. 

Some measures have custom logic:
- Glass is mapped to `complete` if the glass type is HR++ or better, else `to-do`.
- HR-Boiler is mapped to `complete` if it was placed less than 10 years ago, `later` if between 10 and 13
years, and `to-do` if older than 13 years.
- Solar panels are mapped to `complete` if the user has panels and no placing year, or less than 25 years ago, 
and `to-do` if the user has no panels or if the placing year is above 25.
- Ventilation is the most complex; if the user has natural ventilation, it is always `to-do`.
If the user has mechanical ventilation, we check if it is demand driven. If so, `complete`, else `to-do`.
For the other two, we check the heat recovery. If the user has heat recovery, `complete`, else `to-do`.