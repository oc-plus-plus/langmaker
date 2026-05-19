<?php

/**
 * Retrieves a list of files from a specified directory and its subdirectories
 *
 * @param string $sourceDir the directory to scan recursively for files
 *
 * @return array an array of file paths
 */
function getRecursiveFilesRelative(string $sourceDir): array {
	$result = [];

	if (!is_dir($sourceDir)) {
		return $result;
	}

	$sourceDir = rtrim($sourceDir, DIRECTORY_SEPARATOR);

	$directory = new RecursiveDirectoryIterator($sourceDir, RecursiveDirectoryIterator::SKIP_DOTS);
	$iterator = new RecursiveIteratorIterator($directory);

	foreach ($iterator as $file) {
		if ($file->isFile()) {
			$fullPath = $file->getRealPath();

			$relativePath = str_replace($sourceDir, '', $fullPath);
			$result[] = $relativePath;
		}
	}

	return $result;
}

/**
 * Moves a file from the source location to the destination location.
 * If the destination directory does not exist, it attempts to create it.
 *
 * @param string $sourceFile      the path to the source file to be moved
 * @param string $destinationFile the path to the destination where the file should be moved
 * @param bool   $move
 *
 * @return bool true if the file was moved successfully, false otherwise
 */
function copyFile(string $sourceFile, string $destinationFile, bool $move = false): bool {
	if (!is_file($sourceFile)) {
		trigger_error("Source file does not exist: {$sourceFile}", E_USER_WARNING);

		return false;
	}

	$destinationDir = dirname($destinationFile);

	if (!is_dir($destinationDir)) {
		if (!mkdir($destinationDir, 0755, true)) {
			trigger_error("Failed to create directory: {$destinationDir}", E_USER_WARNING);

			return false;
		}
	}

	if ($move && rename($sourceFile, $destinationFile) || copy($sourceFile, $destinationFile)) {
		return true;
	} else {
		trigger_error('Failed to ' . ($move ? 'move' : 'copy') . ' file from ' . $sourceFile . ' to ' . $destinationFile, E_USER_WARNING);

		return false;
	}
}
