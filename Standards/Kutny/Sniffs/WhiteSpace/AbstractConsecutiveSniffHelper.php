<?php

abstract class Kutny_Sniffs_WhiteSpace_AbstractConsecutiveSniffHelper implements \PHP_CodeSniffer\Sniffs\Sniff {
	protected function isEmptyLine($phpcsFile, $stackPtr) {
		return $phpcsFile->findFirstOnLine(array(T_WHITESPACE), $stackPtr, TRUE) === FALSE;
	}
}
