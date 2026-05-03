<?php

define('ROOT_DIR', dirname(__DIR__));
const TEMPLATE_DIR = ROOT_DIR . '/.tools/template/';
const TMP_DIR = ROOT_DIR . '/tmp/';
const LNG_DIR = ROOT_DIR . '/languages/';
const DIST_DIR = ROOT_DIR . '/dist/';

// Connecting functions
require __DIR__ . '/make_dist.functions.php';

// Delete the temporary folder (if any)
delFolderTree(TMP_DIR);

// Get a list of all language modules
$languages = glob(LNG_DIR . '*', GLOB_ONLYDIR);

// Process each of the found language modules separately
foreach ($languages as $dir) {
	////////////////////////////////////////////////////////////
	// Getting the language module configuration              //
	////////////////////////////////////////////////////////////
	if (($config = getConfig($dir . '/config.php')) === []) {
		// If the configuration is incorrect, display a message and skip processing of this module
		echo 'ERROR: empty config for "languages/' . basename($dir) . '"' . PHP_EOL;
		continue;
	}

	$moduleName = 'oc_language_' . basename($dir);
	$moduleDir = TMP_DIR . $moduleName . '/';
	$languageName = $config['settings']['name'];
	$moduleClassName = ucfirst(preg_replace('/[^a-zA-Z0-9]/', '', $languageName));
	$moduleFileName = strtolower($moduleClassName);
	$moduleNameSpace = implode('', array_map('ucfirst', explode('_', $moduleName)));

	$moduleSettingsKey = 'language_' . $moduleFileName;
	$moduleStatusKey = 'language_' . $moduleFileName . '_status';

	///////////////////////////////////////////////////////////////////
	// Creating the extension folder structure in a temporary folder //
	///////////////////////////////////////////////////////////////////
	makeFolderTree($moduleDir, [
		'admin/controller/language',
		'admin/language/en-gb/language',
		'admin/view/template/language',
		'catalog/language',
	]);

	////////////////////////////////////////////////////////////
	// Processing install.json                                //
	////////////////////////////////////////////////////////////
	$installContent = file_get_contents(TEMPLATE_DIR . 'install.dist');
	$installPlaceholders = [
		'%name%'        => 'name',
		'%description%' => 'description',
		'%version%'     => 'version',
		'%author%'      => 'author',
		'%link%'        => 'link'
	];

	// Replace placeholders with values from the language module configuration
	foreach ($installPlaceholders as $placeholder => $key) {
		$installContent = str_replace($placeholder, ($config['install'][$key] ?? ''), $installContent);
	}

	// Write the processed data to a file
	file_put_contents($moduleDir . 'install.json', $installContent);

	////////////////////////////////////////////////////////////
	// Processing the controller template                     //
	////////////////////////////////////////////////////////////
	$controllerContent = file_get_contents(TEMPLATE_DIR . 'controller.dist');
	$controllerPlaceholders = [
		'%module_namespace%'    => $moduleNameSpace,
		'%module_name%'         => $moduleName,
		'%module_filename%'     => $moduleFileName,
		'%module_classname%'    => $moduleClassName,
		'%module_status_key%'   => $moduleStatusKey,
		'%module_settings_key%' => $moduleSettingsKey,
		'%language_name%'       => $languageName,
		'%language_code%'       => $config['settings']['code'],
		'%language_locale%'     => $config['settings']['locale'],
	];

	// Replace placeholders with values
	foreach ($controllerPlaceholders as $placeholder => $key) {
		$controllerContent = str_replace($placeholder, $key, $controllerContent);
	}

	// Write the processed data to a file
	file_put_contents($moduleDir . 'admin/controller/language/' . $moduleFileName . '.php', $controllerContent);

	////////////////////////////////////////////////////////////
	// Processing the language template                       //
	////////////////////////////////////////////////////////////
	$languageContent = file_get_contents(TEMPLATE_DIR . 'language.dist');
	$languageContent = str_replace('%name%', $languageName, $languageContent);
	file_put_contents($moduleDir . 'admin/language/en-gb/language/' . $moduleFileName . '.php', $languageContent);

	////////////////////////////////////////////////////////////
	// Processing the 'view' template                         //
	////////////////////////////////////////////////////////////
	$viewContent = file_get_contents(TEMPLATE_DIR . 'view.dist');
	$viewContent = str_replace('%status%', $moduleStatusKey, $viewContent);
	file_put_contents($moduleDir . 'admin/view/template/language/' . $moduleFileName . '.twig', $viewContent);

	////////////////////////////////////////////////////////////
	// Copy languages                                         //
	////////////////////////////////////////////////////////////
	if (is_dir($dir . '/admin')) {
		copyFolderTree($dir . '/admin', $moduleDir . 'admin/language/' . $config['settings']['code']);
	}

	if (is_dir($dir . '/catalog')) {
		copyFolderTree($dir . '/catalog', $moduleDir . 'catalog/language/' . $config['settings']['code']);
	}

	if (is_dir($dir . '/extension')) {
		$extLngDir = glob($dir . '/extension/*', GLOB_ONLYDIR);

		foreach ($extLngDir as $extLng) {
			if (is_dir($extLng . '/admin')) {
				copyFolderTree($extLng . '/admin', $moduleDir . 'admin/language/' . $config['settings']['code'] . '/extension/' . basename($extLng));
			}

			if (is_dir($extLng . '/catalog')) {
				copyFolderTree($extLng . '/catalog', $moduleDir . 'catalog/language/' . $config['settings']['code'] . '/extension/' . basename($extLng));
			}
		}
	}
}

////////////////////////////////////////////////////////////
// Make OCMOD archives                                    //
////////////////////////////////////////////////////////////
makeOcmodArchives(TMP_DIR, DIST_DIR);

// Delete the temporary folder (if any)
delFolderTree(TMP_DIR);
