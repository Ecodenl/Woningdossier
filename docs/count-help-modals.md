Some code to count / test the ```<i>``` info buttons with its helptexts

## Paste this in the console from the browser
```
let x = $('i.glyphicon.glyphicon-info-sign');
console.log('Info buttons: '+x.length);
let z = $('.modal-dialog.modal-lg');
console.log('Total modals: '+z.length);
$(x).click();
let openedModals = $('.modal-backdrop').length
console.log(openedModals);