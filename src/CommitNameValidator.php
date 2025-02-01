<?php

namespace fperezco\CaptainhookConventionalBranchCommits;

use CaptainHook\App\Exception\ActionFailed;

class CommitNameValidator
{
    public const MESSAGE_INVALID_COMMIT_MESSAGE = "Error: Commit message must follow conventional commit format. For example: 'feat(ISSUE-856): add new feature'.";
    private const STANDARD_COMMIT_PATTERN = '/^(Merge.*|(feat|fix|build|chore|ci|docs|style|refactor|perf|test)(\([A-Za-z0-9-]+\))?: .+)/';
    private ?string $commitPattern;
    private string $currentCommitMessage;

    public function __construct(string $commitMessage,?string $commitPattern = null)
    {
        $this->currentCommitMessage = $commitMessage;
        $this->commitPattern = $commitPattern ?? self::STANDARD_COMMIT_PATTERN;
    }

    /**
     * @throws ActionFailed
     */
    public function validate(): bool
    {
        if(preg_match($this->commitPattern, $this->currentCommitMessage) !== 1){
            throw new ActionFailed($this->getErrorMessage());
        }
        return true;
    }

    public function getPattern(): string
    {
        return $this->commitPattern;
    }

    public function getMessage(): string
    {
        return $this->currentCommitMessage;
    }

    private function getErrorMessage(): string
    {
        if ($this->commitPattern === self::STANDARD_COMMIT_PATTERN) {
            return self::MESSAGE_INVALID_COMMIT_MESSAGE;
        } else {
            return "Error: commit: $this->currentCommitMessage ,must follow the pattern '$this->commitPattern'.";
        }
    }

}