<?php

class Kutny_Sniffs_PHP_DisallowShortOpenTagSniff implements \PHP_CodeSniffer\Sniffs\Sniff {

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(
			T_OPEN_TAG,
		);

	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
	 * @param int                  $stackPtr  The position of the current token
	 *                                        in the stack passed in $tokens.
	 *
	 * @return void
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();
		$openTag = $tokens[$stackPtr];

		if ($openTag['content'] === '<?') {
			$error = 'Short PHP opening tag used; expected "<?php" but found "%s"';
			$data = array($openTag['content']);
			$phpcsFile->addError($error, $stackPtr, 'Found', $data);
		}

	}
	//end process()


}

//end class
