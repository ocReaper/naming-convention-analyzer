<?php

namespace NamingConventionAnalyzer\Sniffs\Variables;

use NamingConventionAnalyzer\Helpers\Inflect;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class SingularPluralSniff implements Sniff
{
    protected const CODE_BOOLEAN_PREFIX = 'SingularPlural';

    protected const ARRAY_OPENERS = [T_ARRAY, T_OPEN_SHORT_ARRAY];

    public function register()
    {
        return static::ARRAY_OPENERS;
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] === T_ARRAY) {
            $phpcsFile->recordMetric($stackPtr, 'Short array syntax used', 'no');
            $arrayStart = $tokens[$stackPtr]['parenthesis_opener'];

            if (isset($tokens[$arrayStart]['parenthesis_closer']) === false) {
                return;
            }

            $arrayEnd = $tokens[$arrayStart]['parenthesis_closer'];
        } else {
            $phpcsFile->recordMetric($stackPtr, 'Short array syntax used', 'yes');
            $arrayStart = $stackPtr;
            $arrayEnd = $tokens[$stackPtr]['bracket_closer'];
        }


        // only check variable definitions. eq according to PSR12
        if ($tokens[$stackPtr - 2]['type'] !== 'T_EQUAL') {
            return;
        }

        // T_VARIABLE must be 4 token before the array start according to PSR12
        if ($tokens[$stackPtr - 4]['type'] !== 'T_VARIABLE') {
            return;
        }

        // check associative arrays
        $doubleArrow = $phpcsFile->findNext(T_DOUBLE_ARROW, $arrayStart, $arrayEnd);

        if ($doubleArrow !== false) {
            $secondLevelArray = $phpcsFile->findNext(self::ARRAY_OPENERS, $arrayStart + 1, $arrayEnd);

            if ($secondLevelArray !== false) {
                if ($doubleArrow < $secondLevelArray) {
                    $phpcsFile->recordMetric($stackPtr, 'Array is associative multi level', 'yes');

                    return;
                } else {
                    $phpcsFile->recordMetric($stackPtr, 'Array is associative multi level', 'no');
                }
            } else {
                $phpcsFile->recordMetric($stackPtr, 'Array is associative', 'yes');

                return;
            }
        }

        $phpcsFile->recordMetric($stackPtr, 'Array is associative', 'no');

        $arrayVariable = $tokens[$stackPtr - 4];

        $arrayVariableName = str_replace(
            '$',
            '',
            $arrayVariable['content']
        );

        $camelCaseArray = preg_split('/(?=[A-Z])/', $arrayVariableName);
        $lastWord = strtolower(array_pop($camelCaseArray));

        if (Inflect::pluralize($lastWord) === $lastWord) {
            return;
        }

        $phpcsFile->addError(
            'Variables containing multiple values should be in plural. Found: %s',
            $stackPtr,
            static::CODE_BOOLEAN_PREFIX,
            [$arrayVariableName]
        );
    }
}
