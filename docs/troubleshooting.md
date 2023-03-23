# Troubleshooting
Having an issue with something? Perhaps another developer has had issues with it before and was smart enough to 
document it.

## Queries
### Re-using (large) queries
In the case of having a large query, you might find that you need to perform it twice using separate callbacks. 
However, when calling things like `$query->where()` you are altering the source query. Therefore, if you were to 
have the following:

```php
$query = DB::table('some_table')->where('some_column', 'some_value');

$sources = $query->where('source', '!=', '5')->get();
$result = $query->where('source', '5')->get();
```

Then, the `$result` would always be empty, since you now have a query that would both query 'source' as equal 
**and** not equal to '5'. Instead, use `clone()`:

```php
$query = DB::table('some_table')->where('some_column', 'some_value');

$sources = $query->clone()->where('source', '!=', '5')->get();
$result = $query->clone()->where('source', '5')->get();
```

This creates clones of the original query, so the original instance is not affected.

## Testing
### Database not refreshing while using `RefreshDatabase`
So, you're making a quality test, and you want to use the `RefreshDatabase` so that the database is fresh after each 
test. And you run the test individually and everything is fine, but now you're running a series of tests and one of 
them fails because there is a database assertion (e.g. `assertDatabaseCount()`) which is retuning more data than 
there should be. The database was not properly refreshed. `RefreshDatabase` uses transactions to roll back made 
changes. This is faster than re-running all migrations, however it is important to note that the transaction may not 
be commited. During a commit, a transaction is saved to the database, and a further rollback will not undo this commit.
Usually however, the failure comes from an implicit commit. This is a commit without explicitly calling `COMMIT`. 
This is from Data definition language (DDL) statements, i.e. schema info altering statements. 

Examples of implicit commits:
- Altering table structures
- Creating new tables
- Resetting auto increment (e.g. by using `truncate()`)
See the [MySQL docs](https://dev.mysql.com/doc/refman/8.0/en/implicit-commit.html) for more info about implicit commits.