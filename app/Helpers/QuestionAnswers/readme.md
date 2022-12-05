## QuestionAnswers

This namespace will contain classes that match the short of a `ToolQuestion`.
These tool questions need some additional code upon saving / updating.

These classes should be triggered before saving the answer to a `ToolQuestion`. 
As for now, this is in the [Form](/app/Http/Livewire/Cooperation/Frontend/Tool/SimpleScan/Form.php).

We want to keep the logic separated from the base code itself.