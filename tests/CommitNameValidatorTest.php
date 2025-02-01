<?php

use fperezco\CaptainhookConventionalBranchCommits\CommitNameValidator;
use PHPUnit\Framework\TestCase;

class CommitNameValidatorTest extends TestCase
{
    /**
     * @dataProvider validCommitMessageProvider
     */
    public function testValidCommitMessage($commitMessage)
    {
        $validator = new CommitNameValidator();
        $this->assertTrue($validator->__invoke($commitMessage));
    }

    /**
     * @dataProvider invalidCommitMessageProvider
     */
    public function testInvalidCommitMessage($commitMessage)
    {
        $validator = new CommitNameValidator();
        $this->assertFalse($validator->__invoke($commitMessage));
    }

    public function validCommitMessageProvider(): array
    {
        return [
            ['feat(RTG-2345): add new feature'],
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
}