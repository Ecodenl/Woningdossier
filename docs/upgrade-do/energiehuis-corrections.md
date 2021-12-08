###Corrections for environments that are deployed in the same manor as Energiehuis.

```sql
set foreign_key_checks = 0; 

update tool_questions
set id = id + 1
where id >= 6
order by id desc;

-- now we correct the example building question
update tool_questions
set id = 6
where id = 52;

-- Corrects the sub step questions
-- 5 becomes empty
update sub_steps
set id = id + 1
where id >= 5
order by id desc;

-- the specific example building 
update sub_steps
set id = 5
where id = 32; 

-- Corrects the sub_step_tool_questions table

update sub_step_tool_questions
set tool_question_id = tool_question_id + 1
where tool_question_id >= 6
order by tool_question_id desc;

-- now we correct the tool questions that are out of order.
update sub_step_tool_questions
set tool_question_id = 6
where tool_question_id = 52;

-- now correct the sub_step_id's
update sub_step_tool_questions
set sub_step_id = sub_step_id + 1
where sub_step_id >= 5
order by sub_step_id desc;

-- the building type 
update sub_step_tool_questions
set sub_step_id = 5
where sub_step_id = 32;

-- Corrects the tool_question_answers

update tool_question_answers
set tool_question_id = tool_question_id + 1
where tool_question_id >= 6
order by tool_question_id desc;

-- the specific example building 
update tool_question_answers
set tool_question_id = 6
where tool_question_id = 52;

-- Corrects the tool_question_custom_values

update tool_question_custom_values
set tool_question_id = tool_question_id + 1
where tool_question_id >= 6
order by tool_question_id desc;


-- the specific example building 
update tool_question_custom_values
set tool_question_id = 6
where tool_question_id = 52; 

-- Corrects the tool_question_valuables

update tool_question_valuables
set tool_question_id = tool_question_id + 1
where tool_question_id >= 6
order by tool_question_id desc;

-- the example building
update tool_question_valuables
set tool_question_id = 6
where tool_question_id = 52; 

set foreign_key_checks = 1;

```