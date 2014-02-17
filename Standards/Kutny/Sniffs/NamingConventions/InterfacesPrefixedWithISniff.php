<?php

/**
 * Prefix interface names with I.
 * @author ondrej
 */
class Kutny_Sniffs_NamingConventions_InterfacesPrefixedWithISniff implements PHP_CodeSniffer_Sniff
{

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_INTERFACE);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
		$tokens = $phpcsFile->getTokens();
		$namePtr = $phpcsFile->findNext(array(T_STRING), $stackPtr);
		if ($namePtr !== FALSE && substr($tokens[$namePtr]['content'], 0, 1) !== 'I') {
			$phpcsFile->addError(
				'Interface name must be prefixed with I.',
				$namePtr
			);
		}

    }//end process()


}//end class

?>
