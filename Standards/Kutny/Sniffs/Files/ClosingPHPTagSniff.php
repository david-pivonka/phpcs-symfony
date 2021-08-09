<?php

/**
 * Disallow closing PHP tag at the end of the file.
 * Using own implementation because Zend's one does not work properly.
 * @author ondrej
 */
class Kutny_Sniffs_Files_ClosingPHPTagSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
		return array(
			T_CLOSE_TAG,
		);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $stackPtr)
	{
		$tokens = $phpcsFile->getTokens();
		$nextOpen = $phpcsFile->findNext(array(T_OPEN_TAG), $stackPtr);
		if ($nextOpen === FALSE) {
			$phpcsFile->addError('Closing tag at the end of the file is disallowed.', $stackPtr);
		}
	}

}//end class

