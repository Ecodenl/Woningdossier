# Non handleable command after reset

### Whats a command?
Referring to anything that can be pushed onto the queue, this is also how the laravel core treats its naming deeper into the source code.

### Feature
It should prevent a command to execute its handle, if it has been pushed onto the queue *and* is picked up by the queue **after** a reset has been done.

### Real world scenario - PRE no
A user completed its dossier, but decides to adjust its `building_features.surface`. This will trigger a full recalculate, these commands will be pushed onto the queue.
However its peak time, the queue is completely full and it wont be picked up immediately. Now the user decides to do a full reset.

This is where the issues arise, the commands are still on the queue and will be picked up after a reset. The recalculations will still be done, they won't be that complete due to missing data but will absolutely "ruin" the reset-ed dossier experience

### How it works
Currently there are 2 classes which prevent a command to complete its handling if a   

