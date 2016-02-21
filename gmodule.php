<?php
/*
 * File name: gmodule.php
 * Author: kohrVid
 * Year: 2016
 */

$Input = "$argv[1]";


##Set Model Names##

function snake_to_camel($phrase) {
	return preg_replace("/\s/", "", ucwords(preg_replace("/\_/", " ", $phrase)));
}

function phrase_to_snake($phrase){
	return ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', $phrase)), '_');
}

function get_model_name($arg){
	if (!isset($arg)){
		echo "Please provide the module name";
		exit;
	}
}

function create_module_folders($arg, $arg2) {
	mkdir("./module/{$arg}/config", 0777, true);
	mkdir("./module/{$arg}/src/{$arg}/Controller/{$arg2}/{$arg2}", 0777, true);
	mkdir("./module/{$arg}/src/{$arg}/Form/{$arg2}/{$arg2}", 0777, true);
	mkdir("./module/{$arg}/src/{$arg}/Model/{$arg2}/{$arg2}", 0777, true);
	mkdir("./module/{$arg}/src/{$arg}/view/{$arg2}/{$arg2}", 0777, true);
}



##Creating the Module.php file##

function create_module_file($input_module) {
	$module_text = <<< EOT
<?php
namespace $input_module;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{
	public function getAutoloaderConfig()
	{
		return array(
			'Zend\Loader\ClassMapAutoloader' => array(
				__DIR__ . '/autoload_classmap.php',
		),
		'Zend\Loader\StandardAutoloader' => array(
			'namespaces' => array(
					__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
				),
			),
		);
	}

	public function getConfig()
	{
		return include __DIR__ . '/config/module.config.php';
	}
}
EOT;

	$module_file = fopen("module/$input_module/Module.php", "w") or die("Unable to create Module.php");
	fwrite($module_file, $module_text);
	fclose($module_file);
}



##Autoloader##

function append_autoloader($file, $contents, $input_module){
	$new_contents = str_replace("", "", $contents);
	$new_contents = str_replace("\"autoload\":", "\"autoload\": {\n\t\t\"psr-0\": {\n\t\t\t\"{$input_module}\": \"module/{$input_module}/src/\"\n\t\t}\n\t}\n}", $contents);
	fclose($file);
	$open_composer_json = fopen("composer.json", "w") or die("Unable to open composer.json");
	fwrite($open_composer_json, $new_contents);
	fclose($composer_json);
}

function set_new_autoloader($file, $contents, $input_module){
	$new_contents = str_replace("}\n}", "},\n\t\"autoload\": {\n\t\t\"psr-0\": {\n\t\t\t\"{$input_module}\": \"module/{$input_module}/src/\"\n\t\t}\n\t}\n}", $contents);
	fclose($file);
	$open_composer_json = fopen("composer.json", "w") or die("Unable to open composer.json");
	fwrite($open_composer_json, $new_contents);
	fclose($open_composer_json);
}

function write_the_autoloader($input_module){
	$composer_json = fopen("composer.json", "r") or die("Unable to open composer.json");
	$composer_json_contents = file_get_contents("composer.json");

	if (strpos($composer_json_contents, "autoloader") == true){
		append_autoloader($composer_json, $composer_json_contents, $input_module);
	}else {
		set_new_autoloader($composer_json, $composer_json_contents, $input_module);
	}
}



##Application Config##

function create_application_config($input_module) {
	$application_config = fopen("config/application.config.php", "r") or die("Unable to open application.config.php");
	$application_config_contents = file_get_contents("config/application.config.php");
	$new_contents = str_replace("Application',", "Application',\n\t\t'{$input_module}',", $application_config_contents);
	fclose($application_config);
	$open_application_config = fopen("config/application.config.php", "w") or die("Unable to open application.config.php");
	fwrite($open_application_config, $new_contents);
	fclose($open_application_config);
}



##Update Composer##

function global_composer_update(){
	exec("composer update");
}

function local_composer_update(){
	exec("php composer.phar update");
}

function update_composer($found_errors) {
	if (file_exists("/usr/bin/composer")) {
		global_composer_update();
	} else if (file_exists("./composer.phar")) {
	local_composer_update();
	} else {
		array_push($found_errors, 'Unable to find composer file - please check that it is installed and/or run "php composer.phar update" manually.');
	}
}



##Module Config##

function create_module_config($camel, $snake) {
		$module_config_file = fopen("./module/$camel/config/module.config.php", "w") or die("Unable to create module.config.php");
		$module_config_text = <<< EOT
<?php
return array(
	'controllers' => array(
		'invokables' => array(
			'$camel\Controller\$camel' => '$camel\Controller\{$camel}Controller',
		),
	),
	'view_manager' => array(
		'template_path_stack' => array(
			'$snake' => __DIR__ . '/../view',
		),
	),
);
EOT;
	fwrite($module_config_file, $module_config_text);
	fclose($module_config_file);
}



##List error messages##

function print_errors($found_errors){
	foreach ($found_errors as $e){
		echo "$e\n";
	}
}


##Here we run the app##

$errors = array();
get_model_name($Input);
$ModelName = snake_to_camel($Input);
$model_name = phrase_to_snake($Input);
create_module_folders($ModelName, $model_name);
create_module_file($ModelName);
write_the_autoloader($ModelName);
create_application_config($ModelName);
update_composer($errors);
create_module_config($ModelName, $model_name);
print_errors($errors);


?>
