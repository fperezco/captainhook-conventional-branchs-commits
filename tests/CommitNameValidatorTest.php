<?php

use CaptainHook\App\Exception\ActionFailed;
use fperezco\CaptainhookConventionalBranchCommits\validators\CommitNameValidator;
use PHPUnit\Framework\TestCase;

class CommitNameValidatorTest extends TestCase
{
    /**
     * @dataProvider validCommitMessageProvider
     */
    public function testValidCommitMessage($commitMessage)
    {
        $validator = new CommitNameValidator($commitMessage);

        try {
            $validator->validate();
        } catch (ActionFailed $e) {
            $this->fail("Expected no exception, but got: " . $e->getMessage());
        }
        $this->addToAssertionCount(1);
    }

    public function validCommitMessageProvider(): array
    {
        return [
            ['feat(RTG-2345): add new feature'],
            ['feat(stuffwithoutspaces): add new feature'],
            ['fix(IDB-89): fix bug'],
            ['build: update dependencies'],
            ['ci: update CI configuration'],
            ['docs: update documentation'],
            ['style: improve code style'],
            ['refactor: refactor code'],
            ['perf: improve performance'],
            ['test: add new tests'],
            ['Merge branch \'feature/RTG-2345-new-user\''],
        ];
    }

    /**
     * @dataProvider invalidCommitMessageProvider
     */
    public function testInvalidCommitMessage($invalidCommitMessage)
    {
        $this->expectException(ActionFailed::class);
        $validator = new CommitNameValidator($invalidCommitMessage);
        $validator->validate();
    }

    public function invalidCommitMessageProvider(): array
    {
        return [
            ['invalid commit message'],
            ['chore(): if nothing inside () not include them is invalid!'],
            ['feat:missing space'],
            ['chore:'],
            ['docs'],
            ['style-'],
        ];
    }

    /**
     * @dataProvider customCommitMessageProvider
     */
    public function testCustomCommitMessagePattern($commitMessage, $shouldThrowException)
    {
        $customPattern = '/^(custom|pattern): [A-Za-z0-9-]+$/';
        $validator = new CommitNameValidator($commitMessage, $customPattern);

        if ($shouldThrowException) {
            $this->expectException(ActionFailed::class);
        }

        $validator->validate();

        if (!$shouldThrowException) {
            $this->assertTrue(true);
        }
    }

    public function customCommitMessageProvider(): array
    {
        return [
            ['custom: valid-message-123', false],
            ['pattern: another-valid-message-456', false],
            ['invalid: message', true],
            ['custom: ', true],
            ['pattern:', true],
            ['custom-message-123', true],
        ];
    }

    public function testInvalidCommitMessageWithDefaultPattern()
    {
        $invalidCommitMessage = 'invalid commit message';
        $validator = new CommitNameValidator($invalidCommitMessage);

        try {
            $validator->validate();
            $this->fail("Expected exception not thrown");
        } catch (ActionFailed $e) {
            $this->assertSame(CommitNameValidator::MESSAGE_INVALID_COMMIT_MESSAGE, $e->getMessage());
        }
    }

    public function testInvalidCommitMessageWithCustomPattern()
    {
        $invalidCommitMessage = 'invalid commit message';
        $customPattern = '/^(custom|pattern): [A-Za-z0-9-]+$/';
        $validator = new CommitNameValidator($invalidCommitMessage, $customPattern);

        try {
            $validator->validate();
            $this->fail("Expected exception not thrown");
        } catch (ActionFailed $e) {
            $expectedMessage = "Error: commit: $invalidCommitMessage ,must follow the pattern '$customPattern'.";
            $this->assertSame($expectedMessage, $e->getMessage());
        }
    }

    public function testCommitMessageWithoutTicketCodeRequiredThrowsException()
    {
        $invalidCommitMessage = 'feat: add new feature';
        $validator = new CommitNameValidator(
            $invalidCommitMessage,
            null,
            true
        );

        $this->expectException(ActionFailed::class);
        $this->expectExceptionMessage("Error: commit message: $invalidCommitMessage ,must include a ticket code following the pattern '/[A-Z]+-[0-9]+/'.");

        $validator->validate();
    }

    public function testCommitMessageWithTicketCodeNotLaunchException()
    {
        $validCommitMessage = 'feat(RTG-2345): add new feature';
        $validator = new CommitNameValidator($validCommitMessage, null, true);

        try {
            $validator->validate();
        } catch (ActionFailed $e) {
            $this->fail("Expected no exception, but got: " . $e->getMessage());
        }
        $this->addToAssertionCount(1);
    }

    public function testCommitMessageWithCustomTicketCodePattern()
    {
        $validCommitMessage = 'feat(CUST-1234): add new feature';
        $customTicketCodePattern = '/CUST-[0-9]{4}/';
        $validator = new CommitNameValidator(
            $validCommitMessage,
            null,
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