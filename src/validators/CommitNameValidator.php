<?php

namespace fperezco\CaptainhookConventionalBranchCommits\validators;

use CaptainHook\App\Exception\ActionFailed;

class CommitNameValidator
{
    public const MESSAGE_INVALID_COMMIT_MESSAGE = "Error: Commit message must follow conventional commit format. For example: 'feat(ISSUE-856): add new feature'.";
    private string $currentCommitMessage;
    private ?string $commitPattern;
    private ?bool $commitShouldIncludeTicketCode;
    private ?string $commitTicketCodePattern;

    public function __construct(
        string $commitMessage,
        ?string $commitPattern = null,
        ?bool $commitShouldIncludeTicketCode = false,
        ?string $commitTicketCodePattern = null
    )
    {
        $this->currentCommitMessage = $commitMessage;
        $this->commitPattern = $commitPattern ?? StandardPatterns::STANDARD_COMMIT_PATTERN;
        $this->commitShouldIncludeTicketCode = $commitShouldIncludeTicketCode;
        $this->commitTicketCodePattern = $commitTicketCodePattern ?? StandardPatterns::STANDARD_BRANCH_AND_COMMIT_TICKET_CODE_PATTERN;
    }

    /**
     * @throws ActionFailed
     */
    public function validate(): void
    {
        if(preg_match($this->commitPattern, $this->currentCommitMessage) !== 1){
            throw new ActionFailed($this->commitMessageInvalidPatternMessage());
        }

        if (
            $this->commitShouldIncludeTicketCode &&
            preg_match($this->commitTicketCodePattern, $this->currentCommitMessage) !== 1) {
            throw new ActionFailed($this->commitTicketCodeInvalidPatternMessage());
        }

    }

    public function getPattern(): string
    {
        return $this->commitPattern;
    }

    public function getMessage(): string
    {
        return $this->currentCommitMessage;
    }

    private function commitMessageInvalidPatternMessage(): string
    {
        if ($this->commitPattern === StandardPatterns::STANDARD_COMMIT_PATTERN) {
            return self::MESSAGE_INVALID_COMMIT_MESSAGE;
        } else {
            return "Error: commit: $this->currentCommitMessage ,must follow the pattern '$this->commitPattern'.";
        }
    }

    private function commitTicketCodeInvalidPatternMessage(): string
    {
        return "Error: commit message: $this->currentCommitMessage ,must include a ticket code following the pattern '$this->commitTicketCodePattern'.";
    }

}