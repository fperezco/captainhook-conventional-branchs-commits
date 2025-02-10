<?php

namespace fperezco\CaptainhookConventionalBranchCommits\helpers;

use CaptainHook\App\Config\Action;

class ActionParametersGetter
{
    public function getStringParam(Action $action, string $parameter): ?string
    {
        return $action->getOptions()->get($parameter);
    }

    public function getBoolParam(Action $action, string $parameter): ?bool
    {
        $value = $action->getOptions()->get($parameter);
        return !$value ? null : $value;
    }
}