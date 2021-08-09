<?php

class Kutny_Lib_ClassNameComposerForward {

	public function composeClassName(\PHP_CodeSniffer\Files\File $phpcsFile, $startPtr) {
		$tokens = $phpcsFile->getTokens();
		$classNameEndPtr = $phpcsFile->findNext(array(T_STRING, T_NS_SEPARATOR), $startPtr, null, true);
		$className = '';

		for ($i = $startPtr; $i < $classNameEndPtr; $i++) {
			$className .= $tokens[$i]['content'];
		}

		return $className;
	}

}
