<?php

/**
 * Retrieves a list of files from a specified directory and its subdirectories,
 * optionally returning paths relative to a given base path.
 *
 * @param string      $sourceDir the directory to scan recursively for files
 * @param string|null $basePath  An optional base path to make file paths relative to.
 *                               If null, absolute paths will be returned.
 *
 * @return array an array of file paths, either absolute or relative to the base path
 */
function getRecursiveFilesRelative(string $sourceDir, ?string $basePath = null): array {
	$result = [];

	if (!is_dir($sourceDir)) {
		return $result;
	}

	$sourceDir = rtrim($sourceDir, DIRECTORY_SEPARATOR);

	if ($basePath !== null) {
		$basePath = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
	}

	$directory = new RecursiveDirectoryIterator($sourceDir, RecursiveDirectoryIterator::SKIP_DOTS);
	$iterator = new RecursiveIteratorIterator($directory);

	foreach ($iterator as $file) {
		if ($file->isFile()) {
			$fullPath = $file->getRealPath();

			if ($basePath !== null) {
				$relativePath = str_replace($basePath, '', $fullPath);
				$result[] = $relativePath;
			} else {
				$result[] = $fullPath;
			}
		}
	}

	return $result;
}

/**
 * Generates a formatted string from a list of file names, excluding files with a .png extension.
 *
 * @param array $list an array of file names to process
 *
 * @return string a formatted string of file names, or 'none' if the list is empty
 */
function createList(array $list): string {
	if (empty($list)) {
		return 'none';
	}

	sort($list);
	$result = '';

	foreach ($list as $file) {
		if (str_ends_with($file, '.png')) {
			continue;
		}

		$result .= '  ' . $file . PHP_EOL;
	}

	return $result;
}
