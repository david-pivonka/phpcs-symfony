<?php

class Kutny_Lib_AllClassFinder  {

	private $phpDocsClassFinder;
	private $classFinder;

	public function __construct(
		Kutny_Lib_PhpDocsClassFinderCached $phpDocsClassFinder,
		Kutny_Lib_ClassFinderCached $classFinder
	) {
		$this->phpDocsClassFinder = $phpDocsClassFinder;
		$this->classFinder = $classFinder;
	}

	public function findClasses(\PHP_CodeSniffer\Files\File $phpcsFile) {
		$usedClasses = $this->classFinder->findClasses($phpcsFile)->getClasses();
		$usedClasseesInPhpDocs = $this->phpDocsClassFinder->findClasses($phpcsFile)->getClasses();

		return $this->formatOutput($usedClasses, $usedClasseesInPhpDocs);
	}

	public function findClassesIncludingFileClass(\PHP_CodeSniffer\Files\File $phpcsFile) {
		$usedClasses = $this->classFinder->findClassesIncludingFileClass($phpcsFile)->getClasses();
		$usedClasseesInPhpDocs = $this->phpDocsClassFinder->findClasses($phpcsFile)->getClasses();

		return $this->formatOutput($usedClasses, $usedClasseesInPhpDocs);
	}

	private function formatOutput(array $usedClasses, array $usedClasseesInPhpDocs) {
		/** @var Kutny_Lib_ClassDefinition[] $mergedClasses */
		$mergedClasses = array_merge($usedClasses, $usedClasseesInPhpDocs);
		$outputClasses = array();
		$fullClassNames = array();

		foreach ($mergedClasses as $class) {
			if (!in_array($class->getFullClassName(), $fullClassNames)) {
				$outputClasses[] = $class;
				$fullClassNames[] = $class->getFullClassName();
			}
		}

		return new Kutny_Lib_ClassDefinitionList($outputClasses);
	}

}
