<?php

class Kutny_Lib_ClassFinder_SimpleParser {

	private $classNameComposerForward;

	public function __construct(Kutny_Lib_ClassNameComposerForward $classNameComposerForward) {
		$this->classNameComposerForward = $classNameComposerForward;
	}

	public function getClassNames(\PHP_CodeSniffer\Files\File $phpcsFile, array $tokensToSearchFor) {
		$start = 0;
		$classNames = array();

		while ($ptr = $phpcsFile->findNext($tokensToSearchFor, $start)) {
			$classNames[] = $this->classNameComposerForward->composeClassName($phpcsFile, $ptr + 2);

			$start = $ptr + 1;
		}

		return $classNames;
	}

}
