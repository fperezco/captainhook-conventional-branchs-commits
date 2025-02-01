<?php

namespace fperezco\CaptainhookConventionalBranchCommits;

use CaptainHook\App\Config\Action;

class ActionParametersGetter
{
    public function __invoke(Action $action, string $parameter): ?string
    {
        return $action->getOptions()->get($parameter);
    }
}