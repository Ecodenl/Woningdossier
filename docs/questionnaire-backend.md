##Questionnaires
####Store
Validation \
The validation array differs from the "normal" array that we will get from the request

A normal array: "questions[new][guid]" \
The validation array: validation[guid or questionid] 

Since the validation can be changed on a existing question we cant set new or edit as array key.
We will set a guid or questionid obtained through the getQuestionId() function this will return
a guid or question id depending on which is available.

Later on in the controller we can check if the array key is a questionid or guid and. If it is a question id
we update the validation for that question, else we search in the questions[new] array to that guid and set the validation for that new question  