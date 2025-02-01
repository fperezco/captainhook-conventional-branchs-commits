<?php

use CaptainHook\App\Config;
use CaptainHook\App\Config\Action;
use CaptainHook\App\Console\IO;
use fperezco\CaptainhookConventionalBranchCommits\CheckConventionalScriptExecutorGateway;
use fperezco\CaptainhookConventionalBranchCommits\helpers\ActionParametersGetter;
use fperezco\CaptainhookConventionalBranchCommits\helpers\CurrentBranchNameGetter;
use fperezco\CaptainhookConventionalBranchCommits\validators\BranchNameValidator;
use fperezco\CaptainhookConventionalBranchCommits\validators\CommitAndBranchSameTicketCodeValidator;
use fperezco\CaptainhookConventionalBranchCommits\validators\CommitNameValidator;
use PHPUnit\Framework\TestCase;
use SebastianFeldmann\Git\CommitMessage;
use SebastianFeldmann\Git\Repository;

class CheckConventionalScriptExecutorGatewayTest extends TestCase
{
    public function testCustomPatternsPassedToValidators()
    {
        // GIVEN
        $customBranchPattern = '/^custom\/branch-[0-9]+$/';
        $customCommitPattern = '/^custom: commit-[0-9]+$/';
        $customTicketCodePattern = '/CUST-[0-9]{4}/';
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
        $actionParametersGetterMock->method('getStringParam')
            ->willReturnMap([
                [$actionMock, 'branchPattern', $customBranchPattern],
                [$actionMock, 'commitPattern', $customCommitPattern],
                [$actionMock, 'commonCommitAndBranchTicketCodePattern', $customTicketCodePattern]
            ]);

        $branchNameValidatorMock = $this->createMock(BranchNameValidator::class);
        $branchNameValidatorMock->method('getPattern')->willReturn($customBranchPattern);
        $branchNameValidatorMock->method('getMessage')->willReturn($branchName);

        $commitNameValidatorMock = $this->createMock(CommitNameValidator::class);
        $commitNameValidatorMock->method('getPattern')->willReturn($customCommitPattern);
        $commitNameValidatorMock->method('getMessage')->willReturn($commitMessage);

        $commitAndBranchSameTicketCodeValidatorMock = $this->createMock(CommitAndBranchSameTicketCodeValidator::class);
        $commitAndBranchSameTicketCodeValidatorMock->method('getPattern')->willReturn($customTicketCodePattern);
        $commitAndBranchSameTicketCodeValidatorMock->method('getBranchName')->willReturn($branchName);
        $commitAndBranchSameTicketCodeValidatorMock->method('getCommitMessage')->willReturn($commitMessage);

        $executor = new CheckConventionalScriptExecutorGateway(
            $branchNameGetterMock,
            $actionParametersGetterMock,
            $branchNameValidatorMock,
            $commitNameValidatorMock,
            $commitAndBranchSameTicketCodeValidatorMock
        );

        // WHEN
        $executor->execute($configMock, $ioMock, $repositoryMock, $actionMock);

        // THEN
        $this->assertSame($customBranchPattern, $branchNameValidatorMock->getPattern());
        $this->assertSame($branchName, $branchNameValidatorMock->getMessage());
        $this->assertSame($customCommitPattern, $commitNameValidatorMock->getPattern());
        $this->assertSame($commitMessage, $commitNameValidatorMock->getMessage());
        $this->assertSame($customTicketCodePattern, $commitAndBranchSameTicketCodeValidatorMock->getPattern());
        $this->assertSame($branchName, $commitAndBranchSameTicketCodeValidatorMock->getBranchName());
        $this->assertSame($commitMessage, $commitAndBranchSameTicketCodeValidatorMock->getCommitMessage());
    }
}