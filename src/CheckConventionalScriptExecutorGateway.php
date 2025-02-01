<?php

namespace fperezco\CaptainhookConventionalBranchCommits;

use CaptainHook\App\Config;
use CaptainHook\App\Config\Action;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Hook\Action as HookAction;
use Exception;
use fperezco\CaptainhookConventionalBranchCommits\helpers\ActionParametersGetter;
use fperezco\CaptainhookConventionalBranchCommits\helpers\CurrentBranchNameGetter;
use fperezco\CaptainhookConventionalBranchCommits\validators\BranchNameValidator;
use fperezco\CaptainhookConventionalBranchCommits\validators\CommitAndBranchSameTicketCodeValidator;
use fperezco\CaptainhookConventionalBranchCommits\validators\CommitNameValidator;
use SebastianFeldmann\Git\Repository as Repo;

class CheckConventionalScriptExecutorGateway implements HookAction
{
    private const GREEN = "\033[0;32m";
    private const NC = "\033[0m";

    private CurrentBranchNameGetter $currentBranchNameGetter;
    private ?ActionParametersGetter $actionParametersGetter;
    private ?BranchNameValidator $branchNameValidator;
    private ?CommitNameValidator $commitNameValidator;
    private ?CommitAndBranchSameTicketCodeValidator $commitAndBranchSameTicketCodeValidator;

    public function __construct(
        ?CurrentBranchNameGetter $currentBranchNameGetter = null,
        ?ActionParametersGetter $actionParametersGetter = null,
        ?BranchNameValidator $branchNameValidator = null,
        ?CommitNameValidator $commitNameValidator = null,
        ?CommitAndBranchSameTicketCodeValidator $commitAndBranchSameTicketCodeValidator = null
    ) {
        $this->currentBranchNameGetter = $currentBranchNameGetter ?? new CurrentBranchNameGetter();
        $this->actionParametersGetter = $actionParametersGetter ?? new ActionParametersGetter();
        $this->branchNameValidator = $branchNameValidator;
        $this->commitNameValidator = $commitNameValidator;
        $this->commitAndBranchSameTicketCodeValidator = $commitAndBranchSameTicketCodeValidator;
    }

    /**
     * Execute the action.
     * @throws Exception
     */
    public function execute(Config $config, IO $io, Repo $repository, Action $action): void
    {
        $commitMessage = $repository->getCommitMsg()->getRawContent();
        $branchName = $this->currentBranchNameGetter->__invoke();

        $branchNameValidator = $this->branchNameValidator ?? new BranchNameValidator(
            $branchName,
            $this->actionParametersGetter->getStringParam($action, 'branchPattern'),
            $this->actionParametersGetter->getBoolParam($action, 'branchShouldIncludeTicketCode'),
            $this->actionParametersGetter->getStringParam($action, 'branchTicketCodePattern')
        );
        $commitNameValidator = $this->commitNameValidator ?? new CommitNameValidator(
            $commitMessage,
            $this->actionParametersGetter->getStringParam($action, 'commitPattern'),
            $this->actionParametersGetter->getBoolParam($action, 'commitShouldIncludeTicketCode'),
            $this->actionParametersGetter->getStringParam($action, 'commitTicketCodePattern')
        );
        $commitAndBranchSameTicketCodeValidator = $this->commitAndBranchSameTicketCodeValidator ?? new CommitAndBranchSameTicketCodeValidator(
            $branchName,
            $commitMessage,
            $this->actionParametersGetter->getBoolParam($action, 'commitAndBranchShouldIncludeTheSameTicketCode'),
            $this->actionParametersGetter->getStringParam($action, 'commitAndBranchCommonTicketCodePattern'),
            $this->actionParametersGetter->getStringParam($action, 'commitAndBranchCommonTicketCodePatternBranchExceptionsPattern'),
        );

        $branchNameValidator->validate();
        $commitNameValidator->validate();
        $commitAndBranchSameTicketCodeValidator->validate();
        $io->write(self::GREEN . 'Right commit format message ;)' . self::NC);
    }
}
