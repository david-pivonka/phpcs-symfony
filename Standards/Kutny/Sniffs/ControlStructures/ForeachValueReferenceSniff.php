<?php

/**
 * Prohibit foreach ($x as &y) { }
 * Because of this behaviour: https://bugs.php.net/bug.php?id=29992
 * @author ondrej
 */
class Kutny_Sniffs_ControlStructures_ForeachValueReferenceSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public function register()
    {
		return array(
			T_FOREACH,
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

		$ampersand = $phpcsFile->findNext(T_BITWISE_AND, $token['parenthesis_opener'], $token['parenthesis_closer']);
		if ($ampersand !== FALSE) {
			$phpcsFile->addError('Passing value as reference in foreach scope is prohibited.', $ampersand);
		}
	}

}
