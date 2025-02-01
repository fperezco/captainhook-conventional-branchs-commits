<?php

use fperezco\CaptainhookConventionalBranchCommits\ActionParametersGetter;
use fperezco\CaptainhookConventionalBranchCommits\BranchNameValidator;
use fperezco\CaptainhookConventionalBranchCommits\CheckConventionalScriptExecutorGateway;
use fperezco\CaptainhookConventionalBranchCommits\CommitNameValidator;
use fperezco\CaptainhookConventionalBranchCommits\CurrentBranchNameGetter;
use CaptainHook\App\Config;
use CaptainHook\App\Config\Action;
use CaptainHook\App\Console\IO;
use SebastianFeldmann\Git\CommitMessage;
use SebastianFeldmann\Git\Repository;
use PHPUnit\Framework\TestCase;

class CheckConventionalScriptExecutorGatewayTest extends TestCase
{
    public function testCustomPatternsPassedToValidators()
    {
        // GIVEN
        $customBranchPattern = '/^custom\/branch-[0-9]+$/';
        $customCommitPattern = '/^custom: commit-[0-9]+$/';
        $branchName = 'custom/branch-123';
        $commitMessage = 'custom: commit-123';

        $branchNameGetterMock = $this->createMock(CurrentBranchNameGetter::class);
        $branchNameGetterMock->method('__invoke')->willReturn($branchName);

        $repositoryMock = $this->createMock(Repository::class);
        $repositoryMock->method('getCommitMsg')->willReturn(new CommitMessage($commitMessage));

        $ioMock = $this->createMock(IO::class);
        $configMock = $this->createMock(Config::class);
        $actionMock = $this->createMock(Action::class);

        $actionParametersGetterMock = $this->createMock(ActionParametersGetter::class);
        $actionParametersGetterMock->method('__invoke')
            ->willReturnMap([
                [$actionMock, 'branchPattern', $customBranchPattern],
                [$actionMock, 'commitPattern', $customCommitPattern]
            ]);

        $branchNameValidatorMock = $this->createMock(BranchNameValidator::class);
        $branchNameValidatorMock->method('getPattern')->willReturn($customBranchPattern);
        $branchNameValidatorMock->method('getMessage')->willReturn($branchName);

        $commitNameValidatorMock = $this->createMock(CommitNameValidator::class);
        $commitNameValidatorMock->method('getPattern')->willReturn($customCommitPattern);
        $commitNameValidatorMock->method('getMessage')->willReturn($commitMessage);

        $executor = new CheckConventionalScriptExecutorGateway(
            $branchNameGetterMock,
            $actionParametersGetterMock,
            $branchNameValidatorMock,
            $commitNameValidatorMock
        );

        // WHEN
        $executor->execute($configMock, $ioMock, $repositoryMock, $actionMock);

        // THEN
        $this->assertSame($customBranchPattern, $branchNameValidatorMock->getPattern());
        $this->assertSame($branchName, $branchNameValidatorMock->getMessage());
        $this->assertSame($customCommitPattern, $commitNameValidatorMock->getPattern());
        $this->assertSame($commitMessage, $commitNameValidatorMock->getMessage());
    }
}