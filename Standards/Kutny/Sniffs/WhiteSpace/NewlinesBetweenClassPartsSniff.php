<?php

/**
 * Checks number of newlines between parts of class or interfaces.
 */
class Kutny_Sniffs_WhiteSpace_NewlinesBetweenClassPartsSniff extends Kutny_Sniffs_WhiteSpace_AbstractConsecutiveSniffHelper
{

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_OPEN_TAG);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile All the tokens found in the document.
     * @param int                  $stackPtr  The position of the current token in
     *                                        the stack passed in $tokens.
	 [$first]['line']);

     * @return void
     */
    public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $stackPtr)
    {
		$tokens = $phpcsFile->getTokens();
		$first = $phpcsFile->findNext(array(T_WHITESPACE), $stackPtr + 1, NULL, TRUE);
		if ($tokens[$first]['line'] != $tokens[$stackPtr]['line'] + 2) {
			$phpcsFile->addError('First PHP token must be on the third line after PHP opening tag.', $first);
		}

		$namespace = $phpcsFile->findNext(array(T_NAMESPACE), $stackPtr + 1);
		if ($namespace !== FALSE && $tokens[$namespace]['content'] === 'namespace'
				&& (!$this->isPreviousLineEmpty($phpcsFile, $namespace)
					|| !$this->isNextLineEmpty($phpcsFile, $namespace))) {
			$phpcsFile->addError('Namespace declaration must be separated by newlines.', $namespace);
		}

		$use = NULL;
		$next = $phpcsFile->findNext(array(T_USE), $stackPtr) ?: 0;
		if ($next && $this->isUseStatement($phpcsFile, $next) && !$this->isPreviousLineEmpty($phpcsFile, $next)) {
			$phpcsFile->addError(
				'Line before first use clausule must be empty.',
				$this->getLastPtrOnPreviousLine($phpcsFile, $next)
			);
		}

		while (TRUE) {
			$next = $phpcsFile->findNext(array(T_USE), $next + 1);
			if ($next === FALSE || isset($tokens[$next]['nested_parenthesis'])) {
				break;
			}
			$use = $next;
		}

		if ($use && $this->isUseStatement($phpcsFile, $use)) {

			if (!$this->isNextLineEmpty($phpcsFile, $use)) {
				$phpcsFile->addError(
					'Line after last use clausule must be empty.',
					$this->getLastPtrOnNextLine($phpcsFile, $use)
				);
			}
		}

		// class or interface check
		$class = $phpcsFile->findNext(array(T_CLASS, T_INTERFACE), $stackPtr);
		if ($class) {
			$name = $tokens[$class]['type'] === 'T_CLASS' ? 'class' : 'interface';
			$classStart = $this->getDocBlockOrDeclarationLine($phpcsFile, $class);

			if ($this->isNextLineEmpty($phpcsFile, $tokens[$class]['scope_opener'])) {
				$phpcsFile->addError(
					'Line after ' . $name . ' open parenthesis must NOT be empty.',
					$this->getLastPtrOnNextLine($phpcsFile, $tokens[$class]['scope_opener'])
				);
			}

			// duplicate error
			/*if (!$this->isPreviousLineEmpty($phpcsFile, $tokens[$class]['scope_closer'])) {
				$phpcsFile->addError(
					'Line before ' . $name  . ' close parenthesis must be empty.',
					$this->getLastPtrOnPreviousLine($phpcsFile, $tokens[$class]['scope_closer'])
				);
			}*/
		}

		// constants check
		$next = $stackPtr;
		while (TRUE) {
			$var = $phpcsFile->findNext(array(T_CONST), $next);
			if ($var === FALSE) {
				break;
			}

			$nextConst = $phpcsFile->findNext(array(T_CONST), $var + 1);

			if ($nextConst && $this->isNextLineEmpty($phpcsFile, $var)) {
				$semicolon = $phpcsFile->findNext(array(T_SEMICOLON), $var);

				$phpcsFile->addError('There must be NO empty lines between constant declarations.', $semicolon + 2);
			}

			if ($nextConst === FALSE && !$this->isNextLineEmpty($phpcsFile, $var)) {

				// multiple declaration by-pass
				$nextEquals = $phpcsFile->findNext(T_EQUAL, $phpcsFile->findNext(T_EQUAL, $var) + 1);
				$semicolon = $phpcsFile->findNext(T_SEMICOLON, $var);

				if ($nextEquals === FALSE || $semicolon < $nextEquals) {
					$phpcsFile->addError('Line after last constant must be empty.', $this->getLastPtrOnNextLine($phpcsFile, $var));
				}
			}

			$next = $var + 1;
		}

		// members check
		if ($class) {
			$next = $stackPtr;
			while (TRUE) {
				$var = $phpcsFile->findNext(array(T_VARIABLE), $next);
				if ($var === FALSE) {
					break;
				}
				if (!$this->isMember($phpcsFile, $var)) {
					$next = $var + 1;
					continue;
				}
				if ($tokens[$var + 1]['type'] === 'T_COMMA') {
					$phpcsFile->addError('Member variables must be separated by a semicolon.', $var);
					$next = $var + 1;
					continue;
				}

				$const = $phpcsFile->findNext(array(T_CONST), $var);
				if ($const !== FALSE) {
					$phpcsFile->addError(
						'All constants must be before class members.',
						$const
					);
				}

				$next = $var + 1;
			}
		}

		// functions check
		$next = $stackPtr;
		while (TRUE) {
			$function = $phpcsFile->findNext(array(T_FUNCTION), $next);
			if ($function === FALSE) {
				break;
			}

			// members -> functions order check
			if ($class && $next === $stackPtr) {
				$var = $function;
				while(TRUE) {
					$found = $phpcsFile->findNext(array(T_VARIABLE), $var, $tokens[$class]['scope_closer']);
					if ($found === FALSE) {
						break;
					}
					if (!$this->isMember($phpcsFile, $found)) {
						$var = $found + 1;
						continue;
					} else {
						$phpcsFile->addError('Class members must be declared before class methods.', $found, 'MembersFunctionsOrder');
					}
					$var = $found + 1;
				}
			}

			// duplicate error
			/*$functionStart = $this->getDocBlockOrDeclarationLine($phpcsFile, $function);
			if (!$this->isPreviousLineEmpty($phpcsFile, $functionStart)) {
				$phpcsFile->addError(
					'Line before function beginning must be empty.',
					$this->getLastPtrOnPreviousLine($phpcsFile, $functionStart)
				);
			}*/

			// if not function definition in interface
			if (isset($tokens[$function]['scope_opener']) && isset($tokens[$function]['scope_closer'])) {
				if ($tokens[$tokens[$function]['scope_opener']]['line'] !== $tokens[$tokens[$function]['scope_closer']]['line'] - 2) { // skip empty functions
					if ($this->isNextLineEmpty($phpcsFile, $tokens[$function]['scope_opener'])) {
						$phpcsFile->addError(
							'Line after function open parenthesis must not be empty.',
							$this->getLastPtrOnNextLine($phpcsFile, $tokens[$function]['scope_opener'])
						);
					}
					if ($this->isPreviousLineEmpty($phpcsFile, $tokens[$function]['scope_closer'])) {
						$phpcsFile->addError(
							'Line before function close parenthesis must not be empty.',
							$this->getLastPtrOnPreviousLine($phpcsFile, $tokens[$function]['scope_closer'])
						);
					}
				}
				$nextFunctionStactPtr = $phpcsFile->findNext(T_FUNCTION, $function + 1);

				if ($nextFunctionStactPtr && !$this->isNextLineEmpty($phpcsFile, $tokens[$function]['scope_closer'])) {
					$phpcsFile->addError(
						'Line after function close parenthesis must be empty.',
						$this->getLastPtrOnNextLine($phpcsFile, $tokens[$function]['scope_closer'])
					);
				}
			}

			$next = $function + 1;
		}

	}//end process()

	private function isMember($phpcsFile, $ptr)
	{
		try {
			$res = $phpcsFile->getMemberProperties($ptr);
			return ($res['scope_specified'] !== FALSE);
		} catch (\PHP_CodeSniffer_Exception $e) {
			return FALSE;
		}
	}

	private function getDocBlockOrDeclarationLine($phpcsFile, $ptr)
	{
		$tokens = $phpcsFile->getTokens();
		$doc = $phpcsFile->findPrevious(array(T_DOC_COMMENT), $ptr);
		if ($doc && $tokens[$doc]['line'] === $tokens[$ptr]['line'] - 1) {
			if (substr($tokens[$doc]['content'], 0, 3) !== '/**') {
				$docStart = $this->findPrevious($phpcsFile, array(T_DOC_COMMENT), $doc - 1, NULL, FALSE, "/**");
			} else {
				$docStart = $doc;
			}
			if ($docStart) {
				return $docStart;
			} else {
				$phpcsFile->addError('Invalid docblock comment.', $doc);
				return $ptr;
			}
		} else {
			return $ptr;
		}
	}

	private function isPreviousLineEmpty($phpcsFile, $ptr)
	{
		$lastPtr = $this->getLastPtrOnPreviousLine($phpcsFile, $ptr);
		return ($lastPtr === FALSE || $this->isEmptyLine($phpcsFile, $lastPtr));
	}

	private function getLastPtrOnPreviousLine($phpcsFile, $stackPtr)
	{
		$tokens = $phpcsFile->getTokens();
		$ptr = $stackPtr - 1;
		while (TRUE) {
			if (!isset($tokens[$ptr])) {
				break;
			}
			if ($tokens[$stackPtr]['line'] === $tokens[$ptr]['line'] + 1) {
				break;
			}
			$ptr--;
		}
		return $ptr;
	}

	private function isNextLineEmpty($phpcsFile, $ptr)
	{
		$nextPtr = $this->getLastPtrOnNextLine($phpcsFile, $ptr);
		return ($nextPtr === FALSE || $this->isEmptyLine($phpcsFile, $nextPtr));
	}

	private function getLastPtrOnNextLine($phpcsFile, $stackPtr)
	{
		// docblock token problem
		$tokens = $phpcsFile->getTokens();
		$ptr = $stackPtr;
		if ($tokens[$stackPtr]['line'] !== $tokens[$ptr]['line'] - 1) {
			while (TRUE) {
				if (!isset($tokens[$ptr])) {
					break;
				}
				if ($tokens[$stackPtr]['line'] === $tokens[$ptr]['line'] - 2) {
					$ptr--;
					break;
				}
				$ptr++;
			}
			if (!isset($tokens[$ptr])) {
				return $ptr-1;
			}
			return $ptr;
		}
		return $res;
	}

	private function isUseStatement($phpcsFile, $stackPtr)
	{
		$afterUse = $stackPtr + 1;
		$tokens = $phpcsFile->getTokens();
		while ($tokens[$afterUse]['type'] === 'T_WHITESPACE') {
			$afterUse++;
		}

		return ($tokens[$afterUse]['type'] !== 'T_OPEN_PARENTHESIS');
	}

	/**
	 * Finds previous token whose value starts with $start parameter.
	 * Copied from \PHP_CodeSniffer\Files\File
	 */
	public function findPrevious(
		$file,
        $types,
        $start,
        $end=null,
        $exclude=false,
        $starts=null,
        $local=false
	) {
		$tokens = $file->getTokens();
        $types = (array) $types;

        if ($end === null) {
            $end = 0;
        }

        for ($i = $start; $i >= $end; $i--) {
            $found = (bool) $exclude;
            foreach ($types as $type) {
                if ($tokens[$i]['code'] === $type) {
                    $found = !$exclude;
                    break;
                }
            }

            if ($found === true) {
                if ($starts === null) {
                    return $i;
                } else if (substr($tokens[$i]['content'], 0, strlen($starts)) === $starts) {
                    return $i;
                }
            }

            if ($local === true && $tokens[$i]['code'] === T_SEMICOLON) {
                break;
            }
        }//end for

        return false;

    }//end findPrevious()


}//end class
