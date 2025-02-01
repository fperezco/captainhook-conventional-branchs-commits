<?php

namespace fperezco\CaptainhookConventionalBranchCommits\validators;

use CaptainHook\App\Exception\ActionFailed;

class BranchNameValidator
{
    public const MESSAGE_INVALID_BRANCH_NAME = "Error: Branch name must be develop/master/release or follow the format 'type/AAA-BBB[-optional-text]'. For example: feature/RTG-2345-new-user or bugfix/IDB-89 or test/FEED-789-other-branch or develop/master/release.";
    private string $currentBranchName;
    private ?string $branchPattern;
    private ?bool $branchShouldIncludeTicketCode;
    private ?string $branchTicketCodePattern;


    //wata
    //private const BRANCH_PATTERN = '/^(develop|master|main|(feature|bugfix|hotfix|chore|release)\/[A-Z]+-[0-9]+(-.*)?)$/';

    public function __construct(
        string $currentBranchName,
        ?string $branchPattern = null,
        ?bool $branchShouldIncludeTicketCode = false,
        ?string $branchTicketCodePattern = null
    ) {
        $this->currentBranchName = $currentBranchName;
        $this->branchPattern = $branchPattern ?? StandardPatterns::STANDARD_BRANCH_PATTERN;
        $this->branchShouldIncludeTicketCode = $branchShouldIncludeTicketCode;
        $this->branchTicketCodePattern = $branchTicketCodePattern ?? StandardPatterns::STANDARD_BRANCH_AND_COMMIT_TICKET_CODE_PATTERN;
    }

    /**
     * @throws ActionFailed
     */
    public function validate(): void
    {
        if (preg_match($this->branchPattern, $this->currentBranchName) !== 1) {
            throw new ActionFailed($this->branchNameInvalidPatternMessage());
        }

        if (
            $this->branchShouldIncludeTicketCode &&
            preg_match($this->branchTicketCodePattern, $this->currentBranchName) !== 1) {
            throw new ActionFailed($this->branchTicketCodeInvalidPatternMessage());
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

    private function branchNameInvalidPatternMessage(): string
    {
        if ($this->branchPattern === StandardPatterns::STANDARD_BRANCH_PATTERN) {
            return self::MESSAGE_INVALID_BRANCH_NAME;
        } else {
            return "Error: branch: '$this->currentBranchName' must follow the pattern '$this->branchPattern'.";
        }
    }

    private function branchTicketCodeInvalidPatternMessage(): string
    {
        return "Error: branch: '$this->currentBranchName' must include a ticket code following the pattern '$this->branchTicketCodePattern'.";
    }

}