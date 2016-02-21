#gmodule for Zend

This is a php app I've created to automate the module creation process in [ZF2](https://github.com/zendframework/ZendSkeletonApplication). Simply add the gmodule.php file to the root of the Zend skeleton application and in the terminal run:

`php gmodule.php module_name`

e.g., If you wanted to create the module "CatsAndDogs" you might type in:

`php gmodule.php cats_and_dogs`

or,

`php gmodule.php CatsAndDogs`

The use of snake_case is advised but CamelCase is also accepted. You may want to remove the file extension so that you can run it as `php gmodule ModuleName`. That's cool too, however there are still a few bugs so use at your own risk.


TODO:
* Error handling for duplicate autoload records, &c.
* Fix issues that occur when a module name is typed in all caps.
* Check that files aren't being wiped when the app is stopped prematurely and handle accordingly.
* Actually test this on something other than a Debian
* Write actual tests for this thing(?)

