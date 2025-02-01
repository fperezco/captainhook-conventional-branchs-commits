<?php

namespace fperezco\CaptainhookConventionalBranchCommits\validators;

class StandardPatterns
{
    public const STANDARD_BRANCH_PATTERN = '/^(develop|master|main|(feature|bugfix|hotfix|chore|release)\/[A-Za-z0-9-]+)$/';
    public const STANDARD_COMMIT_PATTERN = '/^(Merge.*|(feat|fix|build|chore|ci|docs|style|refactor|perf|test)(\([A-Za-z0-9-]+\))?: .+)/';
    public const STANDARD_BRANCH_AND_COMMIT_TICKET_CODE_PATTERN = '/[A-Z]+-[0-9]+/';
}