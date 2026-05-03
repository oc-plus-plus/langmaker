<?php

/**
 * Retrieves the configuration from a specified file.
 *
 * @param string $dir the path to the configuration file
 *
 * @return array the configuration data loaded from the specified file, or an empty array if the file does not exist
 */
function getConfig(string $dir): array {
	if (is_file($dir)) {
		$config = include $dir;

		//TODO: validate the required config values
		return $config;
	}

	return [];
}

/**
 * Creates a folder tree based on the given destination and array of paths.
 *
 * @param string $dest      the base directory where the folder tree will be created
 * @param array  $pathArray an array of paths to be appended to the base directory for creating directories
 *
 * @return void
 */
function makeFolderTree(string $dest, array $pathArray): void {
	foreach ($pathArray as $path) {
		if (!mkdir($dest . $path, 0777, true)) {
			exit('Failed to create directories...');
		}
	}
}

/**
 * Recursively copies the directory structure and files from a source folder to a destination folder.
 *
 * @param string $from The source directory path to copy from. Must be a valid, existing directory.
 * @param string $to   The destination directory path to copy to. If it does not exist, it will be created.
 *
 * @return void this function does not return a value but exits the process on error conditions
 */
function copyFolderTree($from, $to): void {
	$dir = opendir($from);

	if (!is_dir($to)) {
		mkdir($to, 0777, true);
	}

	while (false !== ($file = readdir($dir))) {
		if ($file != '.' && $file != '..') {
			if (is_dir($from . '/' . $file)) {
				copyFolderTree($from . '/' . $file, $to . '/' . $file);
			} else {
				copy($from . '/' . $file, $to . '/' . $file);
			}
		}
	}

	closedir($dir);
}

/**
 * Recursively deletes a directory along with all its files and subdirectories.
 *
 * @param string $dir The directory path to delete. Must be a valid, existing directory.
 *
 * @return bool returns true on successful deletion of the directory and its contents, or false on failure
 */
function delFolderTree(string $dir): bool {
	if (!is_dir($dir)) {
		return false;
	}

	$files = array_diff(scandir($dir), ['.', '..']);

	foreach ($files as $file) {
		is_dir($dir . '/' . $file)
			? delFolderTree($dir . '/' . $file)
			: unlink($dir . '/' . $file);
	}

	return rmdir($dir);
}

/**
 * Creates `.ocmod.zip` archives for each directory found in the source directory.
 *
 * @param string $sourceDir the source directory containing the directories to be archived
 * @param string $destinationDir the destination directory where the archives will be created
 * @param string $zipName the default name of the archive file, which will be modified for each directory; default is '.ocmod.zip'
 *
 * @return void
 */
function makeOcmodArchives(string $sourceDir, string $destinationDir, string  $zipExt = '.ocmod.zip'): void {
	$items = scandir($sourceDir);

	foreach ($items as $item) {
		if ($item === '.' || $item === '..') {
			continue;
		}

		$itemPath = $sourceDir . $item;

		if (is_dir($itemPath)) {
			$zipName = $destinationDir . $item . $zipExt;

			$zip = new ZipArchive();

			if ($zip->open($zipName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
				$files = new RecursiveIteratorIterator(
					new RecursiveDirectoryIterator($itemPath, RecursiveDirectoryIterator::SKIP_DOTS),
					RecursiveIteratorIterator::LEAVES_ONLY
				);

				foreach ($files as $name => $file) {
					$filePath = $file->getRealPath();
					$relativePath = substr($filePath, strlen($itemPath) + 1);
					$zip->addFile($filePath, $relativePath);
				}

				$zip->close();
			} else {
				echo "Error creating archive {$zipName}\n";
			}
		}
	}
}
