<?php

/**
 * Disabled "const FOO = 'foo', BAR = 'bar'" declaration
 */
class Kutny_Sniffs_Classes_MultipleConstantsOrMembersDeclarationSeparatedByCommasSniff
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
			T_CONST,
			T_PUBLIC,
			T_PROTECTED,
			T_PRIVATE,
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

		if ($tokens[$stackPtr]['code'] === T_CONST) {
			$semicolon = $phpcsFile->findNext(T_SEMICOLON, $stackPtr);
			$thisConstant = $phpcsFile->findNext(T_EQUAL, $stackPtr);
			$nextConstant = $phpcsFile->findNext(T_EQUAL, $thisConstant + 1);
			if ($semicolon !== FALSE && $nextConstant !== FALSE && $nextConstant < $semicolon) {
				$phpcsFile->addError('Multiple constants definition separated by commas is prohibited.', $nextConstant, 0);
			}
		} else if ($this->isMember($phpcsFile, $phpcsFile->findNext(T_VARIABLE, $stackPtr))) {
			$semicolon = $phpcsFile->findNext(T_SEMICOLON, $stackPtr);
			$nextMember = $phpcsFile->findNext(T_VARIABLE, $phpcsFile->findNext(T_VARIABLE, $stackPtr) + 1);
			if ($semicolon !== FALSE && $nextMember !== FALSE && $nextMember < $semicolon) {
				$phpcsFile->addError('Multiple members definition separated by commas is prohibited.', $nextMember, 0);
			}
		}
	}

	private function isMember($phpcsFile, $ptr)
	{
		try {
			$res = $phpcsFile->getMemberProperties($ptr);
			return ($res['scope_specified'] !== FALSE);
		} catch (\PHP_CodeSniffer_Exception $e) {
			return FALSE;
		}
	}

}
