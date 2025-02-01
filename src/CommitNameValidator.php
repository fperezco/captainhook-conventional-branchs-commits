<?php

namespace fperezco\CaptainhookConventionalBranchCommits;

class CommitNameValidator
{
    private const COMMIT_PATTERN = '/^(feat|fix|build|chore|ci|docs|style|refactor|perf|test)(\([A-Z0-9-]+\))?: .+/';
    private const MERGE_PATTERN = '/^Merge/';

    public function __invoke(string $commitMsg): bool
    {
        return preg_match(self::COMMIT_PATTERN, $commitMsg) === 1
            || preg_match(self::MERGE_PATTERN, $commitMsg) === 1;
    }
}