<?php

namespace NamingConventionAnalyzer\Sniffs\Functions;

use Illuminate\Support\Pluralizer;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class SingularPluralSniff implements Sniff
{
    protected const CODE_BOOLEAN_PREFIX = 'SingularPlural';
    const ARRAY_OPENERS = [T_ARRAY, T_OPEN_SHORT_ARRAY];

    public function register()
    {
        return static::ARRAY_OPENERS;
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        require_once realpath(__DIR__ . '/../../../vendor') . DIRECTORY_SEPARATOR . 'autoload.php';

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

        if (Pluralizer::plural($arrayVariableName) === $arrayVariableName) {
            return;
        }

        $phpcsFile->addWarning(
            'Variables containing multiple values should be in plural. Found: %s',
            $stackPtr,
            static::CODE_BOOLEAN_PREFIX,
            [$arrayVariableName]
        );
    }
}
