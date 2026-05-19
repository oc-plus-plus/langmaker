<?php

namespace LangMaker;

define('ROOT_DIR', dirname(__DIR__));
const LNG_DIR = ROOT_DIR . '/languages/';

const COLOR_GREEN = "\033[32m";
const COLOR_RED = "\033[31m";
const COLOR_RESET = "\033[0m";

require ROOT_DIR . '/.tools/includes/compareHelpers.php';
require ROOT_DIR . '/.tools/src/CompareLanguages.php';

$options = getopt('', ['master:', 'check:']);

if (!isset($options['check'])) {
	echo '════════════════════════════════════════════════════════════' . PHP_EOL;
	echo 'Usage: ' . COLOR_GREEN . 'php check.php --check french' . COLOR_RESET . PHP_EOL;
	echo '   or  ' . COLOR_GREEN . 'php check.php --master french --check georgian' . COLOR_RESET . PHP_EOL . PHP_EOL;
	echo 'Options:' . PHP_EOL;
	echo '   ' . COLOR_GREEN . '--master' . COLOR_RESET . '  [optional] master language' . PHP_EOL;
	echo '             if not specified, "english" is used by default' . PHP_EOL;
	echo '   ' . COLOR_GREEN . '--check' . COLOR_RESET . '   the language that we will compare with master' . PHP_EOL;
	echo '————————————————————————————————————————————————————————————' . PHP_EOL;
}

if (isset($options['master'])) {
	if (!is_dir(LNG_DIR . $options['master'])) {
		echo COLOR_RED . 'Error: the master language is not found' . COLOR_RESET . PHP_EOL;
	}

	$masterLanguage = $options['master'];
} else {
	$masterLanguage = 'english';
}

if (!isset($options['check'])) {
	echo COLOR_RED . 'Error: the language that we will compare with master is not specified' . COLOR_RESET . PHP_EOL;
	exit;
} elseif (!is_dir(LNG_DIR . $options['check'])) {
	echo COLOR_RED . 'Error: the language that we will compare with master is not found' . COLOR_RESET . PHP_EOL;
	exit;
} else {
	$checkLanguage = $options['check'];
}

// Get a list of files in folders with the master language
$masterFiles = getRecursiveFilesRelative(LNG_DIR . $masterLanguage);
// Get a list of files in folders with the language being checked
$checkFiles = getRecursiveFilesRelative(LNG_DIR . $checkLanguage);

// Copy missing files from the source to the language being checked
$missingFiles = array_diff($masterFiles, $checkFiles);

foreach ($missingFiles as $fileToCopy) {
	if (str_ends_with($fileToCopy, '.php')) {
		copyFile(LNG_DIR . $masterLanguage . '/' . $fileToCopy, LNG_DIR . $checkLanguage . '/' . $fileToCopy);
	}
}

// Move all unnecessary files to the .redundant folder
$extraFiles = array_diff($checkFiles, $masterFiles);

foreach ($extraFiles as $fileToMove) {
	if (!str_starts_with($fileToMove, '/.redundant') && str_ends_with($fileToMove, '.php')) {
		copyFile(LNG_DIR . $checkLanguage . '/' . $fileToMove, LNG_DIR . $checkLanguage . '/.redundant/' . $fileToMove, true);
	}
}

// Сравниваем содержимое файлов
foreach ($masterFiles as $fileToCheck) {
	if (str_starts_with($fileToCheck, '/.redundant')
		|| str_starts_with($fileToCheck, '/config.php')
		|| !str_ends_with($fileToCheck, '.php')) {
		continue;
	}

	$compare = new CompareLanguages(
		LNG_DIR . $masterLanguage . '/' . $fileToCheck,
		LNG_DIR . $checkLanguage . '/' . $fileToCheck
	);

	file_put_contents(LNG_DIR . $checkLanguage . '/' . $fileToCheck, $compare->getResult());
}

echo COLOR_GREEN . 'SUCCESS!' . COLOR_RESET . PHP_EOL;
