pseudo fork teknicode/bootstrap-forms

Updates on process 


## Simple Form Generator

This simple class allows fast generation of html forms with Bootstrap styling.

### Installation

```
composer require teknicode/bootstrap-forms
```

### Usage

```
require("src/Form.php");
use Teknicode\Form\Form;

$form = new Form();
```

Now you're ready to generate a form.
Open the form and pass in an array containing attributes to be included in the form tag. If you don't provide an action attribute the form will submit to its current url.

```
$form->open([
    "id" => "my_form",
    "action" => "my url"
]);
```

Outputs `<form action="my url" id="my_form">`

Add an input to the form. The int in `input(1)` defines the width of the input according to Bootstraps Grid system. For example `input(6)` will set the input width to `col-md-6`.

```
$form->input(1)->set([
    "name"=>"my_first_input",
    "type"=>"text",
    "value" => "The current value of the input",
    "label" => "My First Input"
]);
```

Outputs `<input type="text" name="my_first_input" value="The current value of the input"/>` wrapped with a label.

To add a select.

```
$form->select(2)->set([
    "name"=>"my_select",
    "label"=>"My Select",
    "value" => "option3",
    "options" => [
        "First Group Test"=>"--group--",
        "Option 1" => [
            "value" => "option1",
            "custom-option-attribute" => "attribute value"
        ],
        "Option 2" => "option2",
        "Second Group Test"=>"--group--",
        "Option 3" => "option3",
        "Option 4" => "option4"
    ]
]);
```

Radio button also require an array of options.

```
$form->input(1)->set([
    "name"=>"my_radio",
    "type" => "radio",
    "value" => "value2",
    "label" => "This is a radio button",
    "options" => [
        "option 1" => "value1",
        "option 2" => "value2"
    ]
]);
```

The value attribute will pre select the option from those provided in the options array.

Add html or a spacer.

```
$form->html(
    6, //grid width
    '<b>My Content</b>'
);
```

Add Google ReCaptcha. You will need to create an api key at https://www.google.com/recaptcha/

```
$form->recaptcha("your api public key");
```
NOTE: You must include the following javascript in your page.
`<script src='https://www.google.com/recaptcha/api.js'></script>`

Add a button.

```
$form->button(1)->set([
    "text"=>"My Button",
    "class"=>"btn-lg btn-primary"
]);
```

Now simply output the form.

`echo $form->compile()`

## Catch and Process Posted Data

Initialize the class object

```
use Teknicode\Form\Process;
$process = new Process("mail" or "sms"); //mail is default
```
Provide settings using the set method as show below:
```
$process->set("recipient","email@address.co.uk" /*string or array of strings*/ );
$process->set("from",["address"=>"sender@address.co.uk","name"=>"Sender Name"]);
```
Setup SMTP credentials - skip this to send with mail()
```
$process->set("smtp",[
  "Host"=>"mailerserver.url.com",
  "Username"=>"email@username",
  "Password"=>"AccountPassword",
  "SSL"=>"tls",
  "Port"=>587
]);
```
If you are using SMS, you must provide the following AWS credentials:
```
$process->set("aws", [
   "aws_access_key_id" => "",
   "aws_secret_access_key" => "",
   "default_region" => "eu-west-1",
   "sms_sender_id" => ""
]);

//you would then provide a mobile number in recipient including the country code:
$process->set("recipient","+4407123456789");
```
If you are using MySQLi, you must provide the following credentials:
```
$process->set("mysqli", [
   "host" => "",
   "username" => "",
   "password" => "",
   "database" => "",
   "table" => ""
]);

//you would then provide the values to be set:
$process->set("values",[
  "field name" => "value"
]]);

//to update an existing entry provide the id
$process->set("id",INT);

```

Check Google ReCaptcha (optional)
```
$process->recaptcha("your api private key");
//used to check the recaptcha response
```

Now all thats needed is to catch the post!
```
$send = $process->catch();

if( $send['status']=="failed" ){
  //do something with the error message
  echo $send['error'];

  //in the event of an error the post values are passed back //to repopulate the form
  $send['data'] contains $_POST
}
```
The catch method will parse the posted data and create a simple clean email containing the name of the input and value set.

#### List of available settings

Email
```
$process->set("recipient",STRING | ARRAY OF STRINGS);

$process->set("from",["address"=>STRING,"name"=>STRING]);

$process->set("replyto",STRING);

$process->set("subject",STRING);

process->set("smtp",[
  "Host"=>STRING,
  "Username"=>STRING,
  "Password"=>STRING,
  "SSL"=>STRING,
  "Port"=>INT
]);
```

SMS
```
$process->set("recipient","+4407123456789");

$process->set("aws", [
   "aws_access_key_id" => "",
   "aws_secret_access_key" => "",
   "default_region" => "eu-west-1",
   "sms_sender_id" => ""
]);
```

MySQLi
```
$process->set("id",INT); //optional for update instead of insert

$process->set("mysqli", [
   "host" => "",
   "username" => "",
   "password" => "",
   "database" => "",
   "table" => ""
]);

$process->set("values",[
  "field name" => "value"
]);
```

### License

Copyright 2018 Teknicode

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
