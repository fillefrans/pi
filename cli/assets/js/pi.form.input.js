/**
 * π.form.js
 *
 * Generic form handler for Pi Types, implemented 
 * with HTML5 forms
 *
 * @author Johan Telstad
 * 
 */



/*

HTML5 form input types:

search
email
url
tel
number
email
url
tel
number
range
date
month
week
time
email
url
tel
number
range
date
month
week
time
datetime
datetime-local
color




HTML5 new tags

datalist
label



HTML5 new attributes


placeholder
autofocus
autocomplete
required
pattern
list
multiple
novalidate
formnovalidate
form
formaction
formenctype
formmethod
formtarget


*/

π.require('form');


π.form.input = π.form.input || { loaded : false };


π.form.input.required = false;

