<?php

class Kutny_Lib_ClassFinder_StaticCallsParser {

	private $classNameComposerBackward;

	public function __construct(Kutny_Lib_ClassNameComposerBackward $classNameComposerBackward) {
		$this->classNameComposerBackward = $classNameComposerBackward;
	}

	public function getClassNames(\PHP_CodeSniffer\Files\File $phpcsFile) {
		$start = 0;
		$classNames = array();

		while ($ptr = $phpcsFile->findNext(T_DOUBLE_COLON, $start)) {
			$classNames[] = $this->classNameComposerBackward->composeClassName($phpcsFile, $ptr);

			$start = $ptr + 1;
		}

		return $classNames;
	}

}
