<?php

namespace NamingConventionAnalyzer\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use PHP_CodeSniffer\Sniffs\AbstractScopeSniff;

class ActionPrefixSniff extends AbstractScopeSniff
{
    protected const CODE_ACTION_PREFIX = 'ActionPrefix';

    protected const ACTIONS = [
        'compose',
        'create',
        'delete',
        'fetch',
        'get',
        'handle',
        'remove',
        'reset',
        'set',
    ];

    protected const IGNORED_NAMES = [
        'boot',
        'register',
        'jsonSerialize',
        'offsetExists',
        'offsetGet',
        'offsetSet',
        'offsetUnset',
    ];

    protected const IGNORED_PREFIXES = [
        '__',
        'test',
        'assert'
    ];

    public function __construct()
    {
        parent::__construct(Tokens::$ooScopeTokens, [T_FUNCTION], true);
    }

    protected function processTokenWithinScope(File $phpcsFile, $stackPtr, $currScope)
    {
        $tokens = $phpcsFile->getTokens();

        // Determine if this is a function which needs to be examined.
        $conditions = $tokens[$stackPtr]['conditions'];
        end($conditions);
        $deepestScope = key($conditions);

        if ($deepestScope !== $currScope) {
            return;
        }

        $functionName = $phpcsFile->getDeclarationName($stackPtr);

        $this->evaluate($functionName, $phpcsFile, $stackPtr);
    }

    protected function processTokenOutsideScope(File $phpcsFile, $stackPtr)
    {
        $functionName = $phpcsFile->getDeclarationName($stackPtr);

        if ($functionName === null) {
            return;
        }

        $this->evaluate($functionName, $phpcsFile, $stackPtr);
    }

    protected function isFrameworkFunction(string $name): bool
    {
        if (in_array($name, static::IGNORED_NAMES)) {
            return true;
        }

        foreach (static::IGNORED_PREFIXES as $prefix) {
            if (substr($name, 0, strlen($prefix)) === $prefix) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $functionName
     * @param File $phpcsFile
     * @param int $stackPtr
     */
    protected function evaluate(string $functionName, File $phpcsFile, int $stackPtr): void
    {
        if ($this->isFrameworkFunction($functionName)) {
            return;
        }

        foreach (static::ACTIONS as $action) {
            if (substr($functionName, 0, strlen($action)) === $action) {
                return;
            }
        }

        $phpcsFile->addWarning(
            'Function names should start with action. Found: %s',
            $stackPtr,
            static::CODE_ACTION_PREFIX,
            [trim($functionName)]
        );
    }
}
