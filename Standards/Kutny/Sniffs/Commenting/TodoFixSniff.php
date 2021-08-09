<?php

/**
 * Jyxo_Sniffs_Commenting_FixSniff.
 *
 * Warns about FIX comments.
 */
class Kutny_Sniffs_Commenting_TodoFixSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array(
                                   'PHP',
                                   'JS',
                                  );


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return PHP_CodeSniffer_Tokens::$commentTokens;

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $content = $tokens[$stackPtr]['content'];
        $matches = array();
        if (preg_match('~[^a-z]+(fix|todo)[^a-z]+(.*)~i', $content, $matches) !== 0) {
            // Clear whitespace and some common characters not required at
            // the end of a fix message to make the warning more informative.
            $fixMessage = trim($matches[2]);
            $fixMessage = trim($fixMessage, '[]().');
            $error       = 'Comment refers to a ' . $matches[1] . ' task';
            if ($fixMessage !== '') {
                $error .= " \"$fixMessage\"";
            }

            $phpcsFile->addError($error, $stackPtr);
        }

    }//end process()


}//end class

?>
