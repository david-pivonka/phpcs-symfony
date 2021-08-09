<?php

/**
 * Sets default values for properties in constructor to unify with Doctrine entities
 * (where it's required because of some strange deserialization behaviour).
 * @author ondrej
 */
class Kutny_Sniffs_Classes_DefaultValuesInConstructorSniff
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
			T_VARIABLE,
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
		$isMember = $this->isMember($phpcsFile, $stackPtr);

		if (!$this->isMember($phpcsFile, $stackPtr)) {
			return;
		}

		$properties = $phpcsFile->getMemberProperties($stackPtr);
		if ($properties['is_static'] === TRUE) {
			return;
		}

		if ($properties['scope_specified'] === TRUE && $properties['scope'] === 'public') {
			$docPtr = $phpcsFile->findPrevious(T_DOC_COMMENT, $stackPtr);

			if ($docPtr !== FALSE && $tokens[$docPtr]['line'] === $tokens[$stackPtr]['line'] - 1) {
				$doc = '';
				while ($docPtr !== FALSE && $tokens[$docPtr]['content'] !== "/**\n") {
					$doc = $tokens[$docPtr]['content'] . $doc;
					$docPtr = $phpcsFile->findPrevious(T_DOC_COMMENT, $docPtr - 1);
				}
				if (strpos($doc, '@persistent')) {
					return;
				}
			}
		}

		$nextChar = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, NULL, TRUE);
		if ($nextChar !== FALSE && $tokens[$nextChar]['code'] === T_EQUAL) {
			$phpcsFile->addError('Default values for members must be set in the constructor.', $nextChar, 0);
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

}//end class

