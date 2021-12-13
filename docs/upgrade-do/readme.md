# Upgrade do corrections

## because of various reasons we have to correct data, data you usually dont want to correct.

This directory contains various files that correspond to the woonplan-deltawind environments, these environments will have to be merged together (eventually).
The problem is that those environments have colliding id's in the tool_questions table, meaning the following;

- environment deltawind has id 1 with tool question building-type
- environment hoom has id 1 with tool question heat-recovery

These are broad examples. The other readme's in the current directory will contains queries on how to correct those environments, these queries are required to be executed before merging the environments. 