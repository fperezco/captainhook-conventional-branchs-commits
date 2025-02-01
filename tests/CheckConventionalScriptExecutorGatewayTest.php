<?php


use fperezco\CaptainhookConventionalBranchCommits\CheckConventionalScriptExecutorGateway;
use fperezco\CaptainhookConventionalBranchCommits\CurrentBranchNameGetter;
use CaptainHook\App\Config;
use CaptainHook\App\Config\Action;
use CaptainHook\App\Console\IO;
use SebastianFeldmann\Git\CommitMessage;
use SebastianFeldmann\Git\Repository;
use PHPUnit\Framework\TestCase;
use CaptainHook\App\Exception\ActionFailed;

class CheckConventionalScriptExecutorGatewayTest extends TestCase
{

    public function testMessageIfInvalidBranchName()
    {
        // GIVEN
        $testCommitMessage = 'feat(RTG-2345): add new feature';
        $invalidBranchName = 'invalid-branch-name';
        $branchNameGetterMock = $this->createMock(CurrentBranchNameGetter::class);
        $branchNameGetterMock->method('__invoke')->willReturn($invalidBranchName);

        $repositoryMock = $this->createMock(Repository::class);
        $repositoryMock->method('getCommitMsg')->willReturn(new CommitMessage($testCommitMessage));

        $ioMock = $this->createMock(IO::class);

        $configMock = $this->createMock(Config::class);
        $actionMock = $this->createMock(Action::class);

        $executor = new CheckConventionalScriptExecutorGateway($branchNameGetterMock);

        // THEN
        $this->expectException(ActionFailed::class);
        $this->expectExceptionMessage(CheckConventionalScriptExecutorGateway::MESSAGE_INVALID_BRANCH_NAME);

        // WHEN
        $executor->execute($configMock, $ioMock, $repositoryMock, $actionMock);
    }

    public function testMessageIfInvalidCommit()
    {
        // GIVEN
        $invalidCommitMessage = 'commit message blalbla';
        $validBranchName = 'feature/RTG-valid-branch-name';
        $branchNameGetterMock = $this->createMock(CurrentBranchNameGetter::class);
        $branchNameGetterMock->method('__invoke')->willReturn($validBranchName);

        $repositoryMock = $this->createMock(Repository::class);
        $repositoryMock->method('getCommitMsg')->willReturn(new CommitMessage($invalidCommitMessage));

        $ioMock = $this->createMock(IO::class);

        $configMock = $this->createMock(Config::class);
        $actionMock = $this->createMock(Action::class);

        $executor = new CheckConventionalScriptExecutorGateway($branchNameGetterMock);

        // THEN
        $this->expectException(ActionFailed::class);
        $this->expectExceptionMessage(CheckConventionalScriptExecutorGateway::MESSAGE_INVALID_COMMIT_MESSAGE);

        // WHEN
        $executor->execute($configMock, $ioMock, $repositoryMock, $actionMock);
    }

}