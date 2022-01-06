## Code choices

Sometimes, choices are made. Sometimes, we don't remember why. This is why.
This will help keeping things consistent.

Unlike the [might book of decision making](./the-mighty-book-of-decision-making.md),
these choices are internal. This explained, we can't really ask a non-technical
person what the best way is to handle a technical issue.

### Formatting of numbers
See [might book of decision making](./the-mighty-book-of-decision-making.md) - Number formatting

The choice was made to format certain numbers in certain ways. We achieve this
as follows:
- Sliders are formatted to full integers, always
- Tool questions of type text apply the following:
  - Tool questions with numeric validation will get formatted as requested
  - Tool questions with numeric **and** integer validation will also round to full integers
- Tool questions with numeric **but** of other types will _**NOT**_ receive formatting
  - So basically concluded, we will only format sliders, and text fields
