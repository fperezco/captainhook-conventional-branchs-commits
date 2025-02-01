<?php

use CaptainHook\App\Exception\ActionFailed;
use fperezco\CaptainhookConventionalBranchCommits\validators\CommitAndBranchSameTicketCodeValidator;
use PHPUnit\Framework\TestCase;

class CommitAndBranchSameTicketCodeValidatorTest extends TestCase
{

    public function testBranchAndCommitWithDifferentCodesButValidationDisabledWillNotThrowException()
    {
        $branchName = 'feature/RTG-2345-new-user';
        $commitMessage = 'feat(OTHER-1234): add new feature';
        $validator = new CommitAndBranchSameTicketCodeValidator(
            $branchName,
            $commitMessage,
            false, // Validation flag is disabled
            null,
            null
        );

        try {
            $validator->validate();
        } catch (ActionFailed $e) {
            $this->fail("Expected no exception, but got: " . $e->getMessage());
        }
        $this->addToAssertionCount(1);
    }


    public function testBranchAndCommitWithDifferentCodesValidationEnabledButBranchInExclusionPatternWillNotThrowException()
    {
        $branchName = 'master';
        $commitMessage = 'feat(OTHER-1234): add new feature';
        $validator = new CommitAndBranchSameTicketCodeValidator(
            $branchName,
            $commitMessage,
            true,
            null,
            "/^(develop|master|main)$/"
        );

        try {
            $validator->validate();
        } catch (ActionFailed $e) {
            $this->fail("Expected no exception, but got: " . $e->getMessage());
        }
        $this->addToAssertionCount(1);
    }

    public function testBranchAndCommitWithDifferentCodesValidationEnabledButBranchInExclusionPatternThatNotFillWillThrowException()
    {
        $branchName = 'masterina';
        $commitMessage = 'feat(OTHER-1234): add new feature';
        $validator = new CommitAndBranchSameTicketCodeValidator(
            $branchName,
            $commitMessage,
            true,
            null,
            "/^(develop|master|main)$/"
        );

        $this->expectException(ActionFailed::class);
        $this->expectExceptionMessage("Error: No common ticket code found in both branch name and commit message.");

        $validator->validate();
    }

    /**
     * @dataProvider validBranchAndCommitProvider
     */
    public function testValidBranchAndCommitNoException($branchName, $commitMessage)
    {
        $validator = new CommitAndBranchSameTicketCodeValidator(
            $branchName,
            $commitMessage,
            true,
            null
        );

        try {
            $validator->validate();
        } catch (ActionFailed $e) {
            $this->fail("Expected no exception, but got: " . $e->getMessage());
        }
        $this->addToAssertionCount(1);
    }

    public function validBranchAndCommitProvider(): array
    {
        return [
            ['feature/RTG-2345-new-user', 'feat(RTG-2345): add new feature'],
            ['bugfix/IDB-89-fix-bug', 'fix: balbalb IDB-89 bug'],
            ['hotfix/fix-bug-issue-IDB-4091-beasd', 'hotfix(): fix bug on issue #IDB-4091#'],
            ['hotfix/fix-bug-BLA-965-issue-IDB-4091-beasd', 'chore(): fix AD-18 bug on issue #IDB-4091#'],
        ];
    }

    /**
     * @dataProvider invalidBranchAndCommitProvider
     */
    public function testInvalidBranchAndCommitThrowsException($branchName, $commitMessage)
    {
        $validator = new CommitAndBranchSameTicketCodeValidator(
            $branchName,
            $commitMessage,
            true,
            null
        );

        $this->expectException(ActionFailed::class);
        $this->expectExceptionMessage("Error: No common ticket code found in both branch name and commit message.");

        $validator->validate();
    }

    public function invalidBranchAndCommitProvider(): array
    {
        return [
            ['feature/RTG-2345-new-user', 'feat(OTHER-1234): add new feature'],
            ['bugfix/IDB-89-fix-bug', 'fix(OTHER-5678): fix IDB-96 bug'],
            ['hotfix/IDB-89-fix-bug', 'chore(OTHER-9101): fix bug'],
        ];
    }

    public function testValidBranchAndCommitWithCustomPatternNoException()
    {
        $branchName = 'feature/CUST-1234-new-feature';
        $commitMessage = 'feat(CUST-1234): add new feature';
        $customTicketCodePattern = '/CUST-[0-9]{4}/';
        $validator = new CommitAndBranchSameTicketCodeValidator(
            $branchName,
            $commitMessage,
            true,
            $customTicketCodePattern
        );

        try {
            $validator->validate();
        } catch (ActionFailed $e) {
            $this->fail("Expected no exception, but got: " . $e->getMessage());
        }
        $this->addToAssertionCount(1);
    }
}