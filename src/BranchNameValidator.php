<?php

namespace fperezco\CaptainhookConventionalBranchCommits;

class BranchNameValidator
{

    private const BRANCH_PATTERN = '/^(develop|master|main|(feature|bugfix|hotfix|chore|release)\/[A-Za-z]+(-.*)?)$/';

    //wata
    //private const BRANCH_PATTERN = '/^(develop|master|main|(feature|bugfix|hotfix|chore|release)\/[A-Z]+-[0-9]+(-.*)?)$/';

    public function __invoke(string $branchName): bool
    {
        return preg_match(self::BRANCH_PATTERN, $branchName) === 1;
    }
}