<?php

/**
 * Forbides consecutive newlines.
 */
class Kutny_Sniffs_WhiteSpace_ConsecutiveNewlinesSniff extends Kutny_Sniffs_WhiteSpace_AbstractConsecutiveSniffHelper {

	/**
	 * @var array
	 */
	private $alreadyReported = array();

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(T_OPEN_TAG);

	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile All the tokens found in the document.
	 * @param int                  $stackPtr  The position of the current token in
	 *                                        the stack passed in $tokens.
	 *
	 * @return void
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();
		$lastPtr = $stackPtr;
		while (TRUE) {
			$nextPtr = $phpcsFile->findNext(array(T_WHITESPACE), $lastPtr + 1, NULL, FALSE, "\n");
			if ($nextPtr === FALSE) {
				break;
			}
			if ($this->isEmptyLine($phpcsFile, $lastPtr) && $this->isEmptyLine($phpcsFile, $nextPtr)
				&& $tokens[$lastPtr]['line'] === $tokens[$nextPtr]['line'] - 1
			) {
				$this->alreadyReported[] = $nextPtr;
				if (!in_array($lastPtr, $this->alreadyReported)) {
					$this->alreadyReported[] = $lastPtr;
					$phpcsFile->addError('Two or more consecutive newlines are forbidden.', $nextPtr, 0);
				}
			}
			$lastPtr = $nextPtr;
		}

	}

}
