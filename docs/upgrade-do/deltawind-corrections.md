# Corrects the tool_questions table
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
