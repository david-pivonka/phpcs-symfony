<?php

class Kutny_Lib_PhpDocsClassFinder  {

	private $classNameParser;
	private $classInPhpDocsFinder;

	public function __construct(
		Kutny_Lib_ClassNameParser $classNameParser,
		Kutny_Lib_ClassInPhpDocsFinder $classInPhpDocsFinder
	) {
		$this->classNameParser = $classNameParser;
		$this->classInPhpDocsFinder = $classInPhpDocsFinder;
	}

	public function findClasses(\PHP_CodeSniffer\Files\File $phpcsFile) {
		$tokens = $phpcsFile->getTokens();
		$classes = array();
		$classNames = array();

		foreach ($tokens as $token) {
			if ($token['type'] !== 'T_DOC_COMMENT') {
				continue;
			}

			$class = $this->matchesSymfonyAnnotation($token['content']);

			if (!$class) {
				$class = $this->classInPhpDocsFinder->find($token['content']);
			}

			if (!$class) {
				continue;
			}

			if (!in_array($class->getFullClassName(), $classNames)) {
				$classNames[] = $class->getFullClassName();
				$classes[] = $class;
			}
		}

		return new Kutny_Lib_ClassDefinitionList($classes);
	}

	private function matchesSymfonyAnnotation($phpDocsLine) {
		if (preg_match('~@([A-Z][^(\\\\]+)(\(|\\\\)~', $phpDocsLine, $matches)) {
			return $this->classNameParser->parse($matches[1]);
		}
		else {
			return null;
		}
	}

}
