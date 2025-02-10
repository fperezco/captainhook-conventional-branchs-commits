<?php

namespace fperezco\CaptainhookConventionalBranchCommits\validators;

use CaptainHook\App\Exception\ActionFailed;

class CommitAndBranchSameTicketCodeValidator
{
    private string $branchName;
    private string $commitMessage;
    private ?bool $commitAndBranchShouldIncludeTheSameTicketCode;
    private ?string $commonCommitAndBranchTicketCodePattern;
    private ?string $commitAndBranchCommonTicketCodePatternBranchExceptionsPattern;

    public function __construct(
        string $branchName,
        string $commitMessage,
        ?bool $commitAndBranchShouldIncludeTheSameTicketCode = null,
        ?string $commonCommitAndBranchTicketCodePattern = null,
        ?string $commitAndBranchCommonTicketCodePatternBranchExceptionsPattern = null
    ) {
        $this->branchName = $branchName;
        $this->commitMessage = $commitMessage;
        $this->commitAndBranchShouldIncludeTheSameTicketCode = $commitAndBranchShouldIncludeTheSameTicketCode;
        $this->commonCommitAndBranchTicketCodePattern = $commonCommitAndBranchTicketCodePattern ?? StandardPatterns::STANDARD_BRANCH_AND_COMMIT_TICKET_CODE_PATTERN;
        $this->commitAndBranchCommonTicketCodePatternBranchExceptionsPattern = $commitAndBranchCommonTicketCodePatternBranchExceptionsPattern;
    }

    /**
     * @throws ActionFailed
     */
    public function validate(): void
    {
        if ($this->commitAndBranchShouldIncludeTheSameTicketCode) {
            if ($this->commitAndBranchCommonTicketCodePatternBranchExceptionsPattern &&
                preg_match($this->commitAndBranchCommonTicketCodePatternBranchExceptionsPattern, $this->branchName)) {
                return;
            }

            preg_match_all($this->commonCommitAndBranchTicketCodePattern, $this->branchName, $branchMatches);
            preg_match_all($this->commonCommitAndBranchTicketCodePattern, $this->commitMessage, $commitMatches);

            $branchMatches = $branchMatches[0];
            $commitMatches = $commitMatches[0];

            $commonMatches = array_intersect($branchMatches, $commitMatches);

            if (empty($commonMatches)) {
                throw new ActionFailed("Error: No common ticket code found in both branch name and commit message.");
            }
        }
    }

    public function getPattern(): string
    {
        return $this->commonCommitAndBranchTicketCodePattern;
    }

    public function getCommitMessage(): string
    {
        return $this->commitMessage;
    }

    public function getBranchName(): string
    {
        return $this->branchName;
    }
}