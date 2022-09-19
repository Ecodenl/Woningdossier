# Dump Service
The dump service is a service used to provide data dumps of users, either for a user themselves or because the 
cooperation wants a data overview. 

## Usage
The dump service has been made fluently so it can be easily used within the code.

Example:
```php
use App\Services\DumpService;

// For example purposes
$user = \App\Models\User::first();
$dumpService = DumpService::init()->user($user)->createHeaderStructure();

$dump = $dumpService->generateDump();
$headers = $dumpService->headerStructure;
```

As you can see, generating a dump is very easy. You can easily fetch the headers from the service if you need to 
reuse the translations provided.

## Extra info
### Input source
By default, the master input source is used, so unless you use a different
input source, you don't have to pass it. Otherwise, you can easily do it by calling `->inputSource($inputSource)` on
the service.

### Anonymizing data
You can anonymize the headers (and thus the data in the dump) by simply calling `->anonymize()` on the service. You 
can also pass a boolean if you wish to keep it dynamic.

### Conditional logic
You can also pass a boolean to the `generateDump` method: `->generateDump(true)`. If you do this, conditionally 
unmet questions will not be present in the returned dump. This is **NOT** advised for multi user results, however 
for single user results it _is_ advised, as it will hide questions that the user wouldn't be able to answer anyway 
and keep the structure logical.