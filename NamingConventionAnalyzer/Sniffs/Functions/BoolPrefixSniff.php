<?php

namespace NamingConventionAnalyzer\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class BoolPrefixSniff implements Sniff
{
    protected const CODE_BOOLEAN_PREFIX = 'BoolPrefix';

    protected const ACTIONS = [
        'has',
        'is',
        'should'
    ];

    public function register()
    {
        return [T_FUNCTION];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $properties = $phpcsFile->getMethodProperties($stackPtr);
        $functionName = $phpcsFile->getDeclarationName($stackPtr);

        if ($properties['return_type'] !== 'bool') {
            return;
        }

        foreach (static::ACTIONS as $action) {
            if (substr($functionName, 0, strlen($action)) === $action) {
                return;
            }
        }

        $phpcsFile->addError(
            'Functions returning boolean should start with [has,is,should]. Found: %s',
            $stackPtr,
            static::CODE_BOOLEAN_PREFIX,
            [$functionName]
        );
    }
}
