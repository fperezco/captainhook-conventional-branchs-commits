<?php

namespace fperezco\CaptainhookConventionalBranchCommits;

use CaptainHook\App\Exception\ActionFailed;

class BranchNameValidator
{
    private const STANDARD_BRANCH_PATTERN = '/^(develop|master|main|(feature|bugfix|hotfix|chore|release)\/[A-Za-z0-9-]+)$/';
    public const MESSAGE_INVALID_BRANCH_NAME = "Error: Branch name must be develop/master/release or follow the format 'type/AAA-BBB[-optional-text]'. For example: feature/RTG-2345-new-user or bugfix/IDB-89 or test/FEED-789-other-branch or develop/master/release.";
    private ?string $branchPattern;
    private string $currentBranchName;

    //wata
    //private const BRANCH_PATTERN = '/^(develop|master|main|(feature|bugfix|hotfix|chore|release)\/[A-Z]+-[0-9]+(-.*)?)$/';

    public function __construct(string $currentBranchName, ?string $branchPattern = null)
    {
        $this->currentBranchName = $currentBranchName;
        $this->branchPattern = $branchPattern ?? self::STANDARD_BRANCH_PATTERN;
    }

    /**
     * @throws ActionFailed
     */
    public function validate(): void
    {
        if(preg_match($this->branchPattern, $this->currentBranchName) !== 1){
            throw new ActionFailed($this->getErrorMessage());
        }
    }

    public function getPattern(): string
    {
        return $this->branchPattern;
    }

    public function getMessage(): string
    {
        return $this->currentBranchName;
    }

    private function getErrorMessage(): string
    {
        if ($this->branchPattern === self::STANDARD_BRANCH_PATTERN) {
            return self::MESSAGE_INVALID_BRANCH_NAME;
        } else {
            return "Error: branch: '$this->currentBranchName' must follow the pattern '$this->branchPattern'.";
        }
    }

}