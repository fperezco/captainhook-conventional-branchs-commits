<?php

namespace fperezco\CaptainhookConventionalBranchCommits;

use CaptainHook\App\Config;
use CaptainHook\App\Config\Action;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Exception\ActionFailed;
use CaptainHook\App\Hook\Action as HookAction;
use Exception;
use SebastianFeldmann\Git\Repository as Repo;

//TODO, PENDING INPUT PATTERN PARAMETERS:
//https://chatgpt.com/share/679d0f7c-b72c-8009-9941-c27c54788932
//HAY PUEDO PONER UN INSTALADOR QUE USE LA CONVENCION DE WATA FACTORY EN LUGAR DE LA QUE TENGO CODIFICADA
//ACTUALMENTE PARA STANDARD COMMITS!

//UN BOOL EN PLAN branchshouldincludetiket y el pattern del ticket PARA COMPROBAR ESTO:
//BUENO NO, VALDRIA CON PROPORCIONAR DE ENTRADA OTRO PATTERN PARA EL BRANCH
//Include Jira (or Other Tool) Ticket Numbers:
//If applicable, include the ticket number from your project management tool
//to make tracking easier. For example, for a ticket T-123, the branch name could be feature/T-123-new-login.

//OTRO CHECK PARA OBLIGAR A QUE EL COMMIT TENGA UNA REFERENCIA A UNA TAREA DE JIRA EN EL SCOPE ()
//ESTO IGUAL, VALDRIA CON OTRA EXPRESSION REGULAR

//LO QUE SI NECESITA UN BOOLEANO ES UN CHECK QUE MIRE QUE EN EL COMMIT EN EL SCOPE() SEA IGUAL AL DE LA RAMA
//PARA TODO LO QUE SEA feat(XXXX): babaa baba y eso
//ahi me quedo!!



class CheckConventionalScriptExecutorGateway implements HookAction
{
    private const GREEN = "\033[0;32m";
    private const NC = "\033[0m";
    public const MESSAGE_INVALID_BRANCH_NAME = "Error: Branch name must be develop/master/release or follow the format 'type/AAA-BBB[-optional-text]'. For example: feature/RTG-2345-new-user or bugfix/IDB-89 or test/FEED-789-other-branch or develop/master/release.";
    public const MESSAGE_INVALID_COMMIT_MESSAGE = "Error: Commit message must follow conventional commit format. For example: 'feat(ISSUE-856): add new feature'.";

    private CurrentBranchNameGetter $currentBranchNameGetter;
    private BranchNameValidator $branchNameValidator;
    private ?CommitNameValidator $commitForBranchValidator;

    public function __construct(
        ?CurrentBranchNameGetter $currentBranchNameGetter = null,
        ?BranchNameValidator $branchNameValidator = null,
        ?CommitNameValidator $commitForBranchValidator = null
    ) {
        $this->currentBranchNameGetter = $currentBranchNameGetter ?? new CurrentBranchNameGetter();
        $this->branchNameValidator = $branchNameValidator ?? new BranchNameValidator();
        $this->commitForBranchValidator = $commitForBranchValidator ?? new CommitNameValidator();
    }

    /**
     * Execute the action.
     * @throws Exception
     */
    public function execute(Config $config, IO $io, Repo $repository, Action $action): void
    {
        $commitMessage = $repository->getCommitMsg()->getRawContent();
        $branchName = $this->currentBranchNameGetter->__invoke();

        if (!$this->branchNameValidator->__invoke($branchName)) {
            throw new ActionFailed(self::MESSAGE_INVALID_BRANCH_NAME);
        }

        if(!$this->commitForBranchValidator->__invoke($commitMessage)) {
            throw new ActionFailed(self::MESSAGE_INVALID_COMMIT_MESSAGE);
        }
        $io->write(self::GREEN . 'Right commit format message ;)' . self::NC);
    }
}