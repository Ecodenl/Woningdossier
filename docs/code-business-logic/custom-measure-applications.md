# Custom measure application

## What are they
Measure applications that can be created by a resident or coach.

## How does it work
In an ideal world each custom measure application (hereafter called CMA) would have 1 row.
This is then connected to a coach or resident based on whatever the user does in the frontend, however we cant have 
nice things.

A coach, resident and master can have a CMA, the CMA will have a "hash" so we can keep track of what is what.

For instance the resident can create a CMA and call it "new kitchen", then the coach can come in and change the same 
CMA to "New kitchen doors".

This than should be changed on the master input source its CMA, that's why we have a hash. It keeps the relation 
between the 3 "different" rows but same CMAs. 

## Measure Categories
Within the upgrade of the subsidy API, we decided to use a `mappings` table to store data from the API internally, 
so we wouldn't constantly have to do calls to the API to see the values a user has chosen. For CMAs, we save a 
measure category. As read above, there can be more than one row for the CMA. Within Hoomdossier, however, we pretty 
much always read from the master. In most cases within the Hoomdossier, a user is able to compare answers from the 
coach and resident, to see where an answer on the master has come from. This isn't available for CMAs. 
Therefore we have decided to only save the selected measure category on the master input source, since that's the 
only row that will ever be read from anywhere. 