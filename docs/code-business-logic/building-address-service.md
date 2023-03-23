# Building Address Service
##### Service made to help with anything address - building related.

## attachMunicipality
The method name itself is pretty explanatory, it attaches a municipality to the building. Which is based on the current address data of the building.
There are a few if-elses which are important and completely based on business logic

- Its possible we cant resolve a municipality name from the bag, this means that either the address is not known by bag or bag is down. If so we cant do anything here, we do nothing
- If a municipality name is resolved from the bag we can try to find it in our database
  - If we have a mapping to a municipality model we will set that municipality on the building.
    - If that model does not have a mapping to a verbeterjehuis gemeente we will notify the admin mails (support) 
  - So its also possible the municipality model does not exist, we will unset the previous municipality on the building
    - If that bag municipality doesnt have any mapping yet we will create a targetless mapping **.



## Targetless mapping
This is created so we can easily show the target less mappings, this will create a easier ux for the admin while mapping the values.