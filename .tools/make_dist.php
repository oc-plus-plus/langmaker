<?php

$sourceDir = dirname(__DIR__) . '/oc_language';
$destDir = dirname(__DIR__) . '/dist';

$items = scandir($sourceDir);

foreach ($items as $item) {
	if ($item === '.' || $item === '..') continue;

	$itemPath = $sourceDir . '/' . $item;

	if (is_dir($itemPath)) {
		$zipName = $destDir . '/oc_language_' . $item . '.ocmod.zip';
		echo "Archiving a folder: $item -> $zipName... ";

		$zip = new ZipArchive();

		if ($zip->open($zipName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
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
			echo "Done!\n";
		} else {
			echo "Error creating archive $zipName\n";
		}
	}
}
