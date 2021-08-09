<?php

/**
 * Class name in use clause must not start with backslash.
 * @author ondrej
 */
class Kutny_Sniffs_Namespaces_ClassNameInUseClausuleMustNotStartWithBackslashSniff
	implements \PHP_CodeSniffer\Sniffs\Sniff
{

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
		return array(
			T_USE,
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
		$token = $tokens[$stackPtr];
		if ($tokens[$stackPtr+2]['type'] == 'T_NS_SEPARATOR') {
			$phpcsFile->addError('Class name in use clausule must not start with backslash.', $stackPtr, 0);
		}
	}//end process()

}//end class

