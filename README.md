# MDParse

This is a simple markdown file parser written in PHP in a single file

## How to use

It's simple. You just have to download the `guideline.php` file and install the composer dependencies. That's it.

In case you're using an external composer directory, you just have to change the first line:
```php
<?php
- require("./vendor/autoload.php");
+ require("../your/path/to/vendor/autoload.php");

...
```

## Customization

You probably want to change the name of the parser file. The best part, you can easily do that. Just rename the file and everything will be fine, no questions asked.

In case your source file has another name, open up `guideline.php` (or how you named it) and make the following change:
```php
...

- $file = "guideline.md";
+ $file = "anything-you-want.md";

...
```
