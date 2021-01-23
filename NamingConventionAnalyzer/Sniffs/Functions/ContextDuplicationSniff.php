<?php

namespace NamingConventionAnalyzer\Sniffs\Functions;

use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class ContextDuplicationSniff implements Sniff
{
    protected const CODE_CONTEXT_DUPLICATION = 'ContextDuplication';

    public function register()
    {
        return [T_FUNCTION, T_VARIABLE];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['type'] === 'T_VARIABLE') {
            try {
                $phpcsFile->getMemberProperties($stackPtr);
            } catch (RuntimeException $e) {
                return;
            }

            $tokenName = str_replace('$', '', $tokens[$stackPtr]['content']);
        } else {
            $tokenName = $phpcsFile->getDeclarationName($stackPtr);
        }

        $classPtr = $phpcsFile->findPrevious(Tokens::$ooScopeTokens, $stackPtr);
        $className = $phpcsFile->getDeclarationName($classPtr);

        if (strstr(strtolower($tokenName), strtolower($className)) === false) {
            return;
        }

        $phpcsFile->addError(
            'A name should not duplicate the context in which it is defined. Found: %s',
            $stackPtr,
            static::CODE_CONTEXT_DUPLICATION,
            [$tokenName]
        );
    }
}
