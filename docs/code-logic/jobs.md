An explanation for jobs that may seem confusing.
-----

### CheckBuildingAddress
This job is there to update the building its address and municipality with data from the BAG API.
It will use the address data from the building itself.
It's used upon login and register, it may be released on the queue in case we "failed" to retrieve a municipality.
This job only applies for cooperations in the Netherlands, since that's the only place the BAG API is supported.