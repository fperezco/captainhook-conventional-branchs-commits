<?php

namespace fperezco\CaptainhookConventionalBranchCommits\helpers;

class CurrentBranchNameGetter
{
    public function __invoke(): string
    {
        return trim(shell_exec('git rev-parse --abbrev-ref HEAD'));
    }
}