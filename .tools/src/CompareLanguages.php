<?php

namespace LangMaker;

class CompareLanguages {
	private array $sourceFileLines;
	private array $sourceLanguage;
	private array $compareLanguage;

	private const FIND_KEY_VALUE_PATTERN = '/^\s*\$_\[[\'"](?<key>.+?)[\'"]\]\s*=\s*[\'"](?<value>.+?)[\'"]/';
	private const REPLACE_VALUE_PATTERN = '/(.*=\s*)[\'].*[\'](;?)/';

	private const LM_ADDED_MESSAGE = '/* LM ADDED */ ';
	private const LM_REMOVED_MESSAGE = "\n/**\n * LM REMOVED\n * These keys were redundant and were removed.\n */";

	public function __construct(string $sourceFile, string $compareFile) {
		$sourceFile = $this->requireExistingFile($sourceFile);
		$compareFile = $this->requireExistingFile($compareFile);

		$this->sourceFileLines = $this->readLines($sourceFile);
		$this->sourceLanguage = $this->loadLanguage($sourceFile);
		$this->compareLanguage = $this->loadLanguage($compareFile);
	}

	/**
	 * Processes the source language file and compares it with the compare language.
	 * It returns a formatted string with the results of the comparison.
	 *
	 * @return string a formatted string containing the comparison results
	 */
	public function getResult(): string {
		$result = [];

		foreach ($this->sourceFileLines as $line) {
			$result[] = $this->buildResultLine($line);
		}

		$this->appendRedundantKeys($result);

		return implode("\n", $result);
	}

	/**
	 * Processes a given line by parsing key-value pairs.
	 *
	 * @param string $line the input line to process
	 *
	 * @return string the processed line with potential modifications applied
	 */
	private function buildResultLine(string $line): string {
		// If the line does not match the expected pattern, it is returned as is.
		if (!preg_match(self::FIND_KEY_VALUE_PATTERN, $line, $matches)) {
			return $line;
		}

		$key = $matches['key'];

		// If the key does not exist in the compare language, it adds a comment, and the base language line is used
		if (!array_key_exists($key, $this->compareLanguage)) {
			return self::LM_ADDED_MESSAGE . $line;
		}

		// If the key exists in the compare language, it replaces the value
		return $this->replaceLineValue($line, $this->compareLanguage[$key]);
	}

	/**
	 * Replaces a specific value within a line of text using a predefined pattern.
	 *
	 * @param string $line  the input string containing the line to be modified
	 * @param string $value the new value to be inserted into the line
	 *
	 * @return string the modified line with the replacement applied according to the pattern
	 */
	private function replaceLineValue(string $line, string $value): string {
		return preg_replace(self::REPLACE_VALUE_PATTERN, '${1}\'' . addcslashes($value, '\\\'') . '\'${2}', $line);
	}

	/**
	 * Checks if the specified file exists and returns its path.
	 *
	 * @param string $file the path to the file to check for existence
	 *
	 * @throws \InvalidArgumentException if the file does not exist
	 *
	 * @return string the path to the file if it exists
	 */
	private function requireExistingFile(string $file): string {
		if (!file_exists($file)) {
			throw new \InvalidArgumentException("File {$file} does not exist.");
		}

		return $file;
	}

	/**
	 * Reads all lines from the specified file and returns them as an array.
	 *
	 * @param string $file the path to the file to read
	 *
	 * @throws \RuntimeException if the file cannot be read
	 *
	 * @return array an array of lines read from the file
	 */
	private function readLines(string $file): array {
		$lines = file($file, FILE_IGNORE_NEW_LINES);

		if ($lines === false) {
			throw new \RuntimeException("Failed to read file {$file}.");
		}

		return $lines;
	}

	/**
	 * Retrieves an array of language data from the specified file.
	 *
	 * @param string $file the path to the file to include, which should return an array
	 *
	 * @return array the array of language data, or an empty array if none is found
	 */
	private function loadLanguage(string $file): array {
		return (function() use ($file): array {
			$_ = [];

			include $file;

			return $_;
		})();
	}

	/**
	 * Identifies redundant keys, present in the compare Language but missing in the source Language
	 * and appends information about these redundant keys to the result array.
	 *
	 * @param array $result an array to collect messages regarding redundant keys found
	 *
	 * @return void
	 */
	private function appendRedundantKeys(array &$result): void {
		$extraKeys = array_diff_key($this->compareLanguage, $this->sourceLanguage);

		if (empty($extraKeys)) {
			return;
		}

		$result[] = self::LM_REMOVED_MESSAGE;

		foreach ($extraKeys as $key => $value) {
			$result[] = '// $_[\'' . addcslashes($key, '\\\'') . '\'] = \'' . addcslashes($value, '\\\'') . '\';';
		}
	}
}
