<?php

namespace NamingConventionAnalyzer\Sniffs\Functions;

use NamingConventionAnalyzer\Helpers\ContextDuplicationSniff as BaseContextDuplicationSniff;

class ContextDuplicationSniff extends BaseContextDuplicationSniff
{
    public function register()
    {
        return [T_FUNCTION];
    }
}
