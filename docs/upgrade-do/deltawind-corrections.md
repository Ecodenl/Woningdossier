## Corrections for environments that are deployed in the same manor as Deltawind

### Corrects the tool_questions table
```sql
set foreign_key_checks = 0;
-- increments the id's its necessery 
update tool_questions
set id = id + 2
where id >= 5 
order by id desc;

-- increments the id's below 5 with 2
update tool_questions 
set id = id + 1
where id <= 4
order by id desc;

-- now we correct the tool questions that are out of order.
update tool_questions
set id = 1
where short = "building-type-category";

update tool_questions
set id = 6
where short = "specific-example-building"
```
### Corrects the sub step questions
```sql
set foreign_key_checks = 0;

update sub_steps
set id = id + 2
where id >= 4
order by id desc;

update sub_steps
set id = id + 1
where id <= 3
order by id desc;
-- the building type 
update sub_steps
set id = 1
where id = 32;
-- the specific example building 
update sub_steps
set id = 5
where id = 34; 
```

### Corrects the sub_step_tool_questions table
```sql
-- This is a bit more complex, we have to update the tool_question_id and sub_step_id 
-- increments the id's its necessary
set foreign_key_checks = 0;

update sub_step_tool_questions
set tool_question_id = tool_question_id + 2
where tool_question_id >= 5 
order by tool_question_id desc;

-- increments the id's below 5 with 2
update sub_step_tool_questions 
set tool_question_id = tool_question_id + 1
where tool_question_id <= 4
order by tool_question_id desc;

-- now we correct the tool questions that are out of order.
update sub_step_tool_questions
set tool_question_id = 1
where id = 50;

update sub_step_tool_questions
set tool_question_id = 6
where id = 51;

-- now correct the sub_step_id's
update sub_step_tool_questions
set sub_step_id = id + 2
where sub_step_id >= 4
order by sub_step_id desc;

update sub_step_tool_questions
set sub_step_id = sub_step_id + 1
where sub_step_id <= 3
order by sub_step_id desc;
-- the building type 
update sub_step_tool_questions
set sub_step_id = 1
where sub_step_id = 32;
-- the specific example building 
update sub_step_tool_questions
set sub_step_id = 5
where sub_step_id = 33; 
```

###Corrects the tool_question_answers
```sql
set foreign_key_checks = 0;

update tool_question_answers
set tool_question_id = tool_question_id + 2
where tool_question_id >= 4
order by tool_question_id desc;

update tool_question_answers
set tool_question_id = tool_question_id + 1
where tool_question_id <= 3
order by tool_question_id desc;
-- the building type 
update tool_question_answers
set tool_question_id = 1
where tool_question_id = 30;

-- the specific example building 
update tool_question_answers
set tool_question_id = 5
where tool_question_id = 31; 
```

###Corrects the tool_question_custom_values
```sql
set foreign_key_checks = 0;

update tool_question_custom_values
set tool_question_id = tool_question_id + 2
where tool_question_id >= 4
order by tool_question_id desc;

update tool_question_custom_values
set tool_question_id = tool_question_id + 1
where tool_question_id <= 3
order by tool_question_id desc;
-- the building type 
update tool_question_custom_values
set tool_question_id = 1
where tool_question_id = 30;

-- the specific example building 
update tool_question_custom_values
set tool_question_id = 5
where tool_question_id = 31; 
```

###Corrects the tool_question_valuables
```sql
set foreign_key_checks = 0;

update tool_question_valuables
set tool_question_id = tool_question_id + 2
where tool_question_id >= 4
order by tool_question_id desc;

update tool_question_valuables
set tool_question_id = tool_question_id + 1
where tool_question_id <= 3
order by tool_question_id desc;
-- the building type 
update tool_question_valuables
set tool_question_id = 1
where tool_question_id = 30;

-- the specific example building 
update tool_question_valuables
set tool_question_id = 5
where tool_question_id = 31; 
```