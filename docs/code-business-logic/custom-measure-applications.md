# Custom measure application

## What are they
Measure applications that can be created by a resident or coach.

## How does it work
In an ideal world each custom measure application (hereafter called CMA) would have 1 row.
This is then connected to a coach or resident based on whatever the user does in the frontend, however we cant have nice things.

A coach, resident and master can have a CMA, the CMA will have a "hash" so we can keep track of what is what.

For instance the resident can create a CMA and call it "new kitchen", then the coach can come in and change the same CMA to "New kitchen doors".

This than should be changed on the master input source its CMA, thats why we have a hash. It keeps the relation between the 3 "different" rows but same CMA's. 