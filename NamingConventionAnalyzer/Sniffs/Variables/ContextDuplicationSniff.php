<?php

namespace NamingConventionAnalyzer\Sniffs\Variables;

use NamingConventionAnalyzer\Helpers\ContextDuplicationSniff as BaseContextDuplicationSniff;

class ContextDuplicationSniff extends BaseContextDuplicationSniff
{
    public function register()
    {
        return [T_VARIABLE];
    }
}
