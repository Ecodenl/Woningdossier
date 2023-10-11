# Non handleable command after reset

### Whats a command?
Referring to anything that can be pushed onto the queue, this is also how the laravel core treats its naming deeper into the source code.

### Real world scenario and problem
A user completed its dossier, but decides to adjust its `building_features.surface`. This will trigger a full recalculate, these commands will be pushed onto the queue.
However its peak time, the queue is completely full and it wont be picked up immediately. Now the user decides to do a full reset.

This is where the issues arise, the commands are still on the queue and will be picked up after a reset. The recalculations will still be done, they won't be that complete due to missing data but will absolutely "ruin" the reset-ed dossier experience

### The feature that solves the problem
It should prevent a command to execute its handle, if it has been pushed onto the queue *and* is picked up by the queue **after** a reset has been done.

### How it works
When creating a listener or job you should extend the correct class, for a job you should extend `App\Jobs\NonHandleableJobAfterReset` or for a listener extend `App\Listeners\NonHandleableListenerAfterReset`.

The construct of a command is executed upon dispatch, so before its picked up by the queue. Here the "queuedAt" wil be registered.

Usually you have a different construct in the command, always be sure to call the parent construct or else it wont work, the construct sets the timestamp and is crucial to work.
#### Job
The job can leverage middlewares, this is done by default when extending the proper class. However you may need additional middlewares, in that case add the middleware youreself:

```php
   new CheckLastResetAt($this->building);
```

#### Listener
A listener does not have middlewares, as time of writing. Thus you should wrap all logic in the `checkLastResetAt` method, a better example:
```php
    public function handle($event)
    {
        $this->checkLastResetAt(function () use ($event) {
            RefreshRegulationsForBuildingUser::dispatch($event->building);
        }, $event->building);
    }
```

 


