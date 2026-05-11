<?php

define('ROOT_DIR', dirname(__DIR__));
const LNG_DIR = ROOT_DIR . '/languages/';

const COLOR_GREEN = "\033[32m";
const COLOR_RED = "\033[31m";
const COLOR_RESET = "\033[0m";

require __DIR__ . '/includes/compareHelpers.php';

$options = getopt("help", ["master:", "check:"]);

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
} else {
	$checkLanguage = $options['check'];
}

$replace['%master_language%'] = $masterLanguage;
$replace['%check_language%'] = $checkLanguage;

// Get a list of files in folders with the master language
$masterFiles = getRecursiveFilesRelative(LNG_DIR . $masterLanguage, LNG_DIR . $masterLanguage);
// Get a list of files in folders with the language being checked
$checkFiles = getRecursiveFilesRelative(LNG_DIR . $checkLanguage, LNG_DIR . $checkLanguage);

// Calculate which files are missing in the language being checked
$missingFiles = array_diff($masterFiles, $checkFiles);
$replace['%missing_files%'] = createList($missingFiles);

// Calculate which files are extra in the language being checked
$extraFiles = array_diff($checkFiles, $masterFiles);
$replace['%extra_files%'] = createList($extraFiles);

$report = file_get_contents(__DIR__ . '/template/compare.txt');

foreach ($replace as $key => $value) {
	$report = str_replace($key, $value, $report);
}

file_put_contents(ROOT_DIR . '/compare-' . $masterLanguage . '-' . $checkLanguage . '.txt', $report);

echo COLOR_GREEN . 'SUCCESS!' . COLOR_RESET . PHP_EOL;
echo COLOR_GREEN . 'The comparison report has been saved to: ' . COLOR_RESET . 'compare-' . $masterLanguage . '-' . $checkLanguage . '.txt' . PHP_EOL;
