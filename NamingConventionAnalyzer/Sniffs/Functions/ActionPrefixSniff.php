<?php

namespace NamingConventionAnalyzer\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use PHP_CodeSniffer\Sniffs\AbstractScopeSniff;

class ActionPrefixSniff extends AbstractScopeSniff
{
    protected const CODE_ACTION_PREFIX = 'ActionPrefix';

    protected const IGNORED_NAMES = [
        'jsonSerialize',
        'offsetExists',
        'offsetGet',
        'offsetSet',
        'offsetUnset',
    ];

    protected const PREFIXES = [
        'has',
        'is',
        'should'
    ];

    /**
     * @var string[]
     */
    protected $verbs;

    public function __construct()
    {
        $lexicon = file_get_contents(realpath(__DIR__ . '/../../NLP') . DIRECTORY_SEPARATOR . 'verb-lexicon.txt');
        $this->verbs = explode("\n", $lexicon);

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
        if (substr($name, 0, 2) === '__') {
            return true;
        }

        if (in_array($name, static::IGNORED_NAMES)) {
            return true;
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

        $camelCaseArray = preg_split('/(?=[A-Z])/', $functionName);

        $actionPart = $camelCaseArray[0];

        if (in_array($actionPart, static::PREFIXES, true)) {
            $actionPart = strtolower($camelCaseArray[1] ?? '');
        }

        if (in_array($actionPart, $this->verbs, true)) {
            return;
        }

        $phpcsFile->addWarning(
            'Function names should start with action. Found: %s',
            $stackPtr,
            static::CODE_ACTION_PREFIX,
            [trim($functionName)]
        );
    }
}
