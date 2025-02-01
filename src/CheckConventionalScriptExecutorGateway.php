<?php

namespace fperezco\CaptainhookConventionalBranchCommits;

use CaptainHook\App\Config;
use CaptainHook\App\Config\Action;
use CaptainHook\App\Console\IO;
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

    private CurrentBranchNameGetter $currentBranchNameGetter;
    private ?ActionParametersGetter $actionParametersGetter;
    private ?BranchNameValidator $branchNameValidator;
    private ?CommitNameValidator $commitNameValidator;

    public function __construct(
        ?CurrentBranchNameGetter $currentBranchNameGetter = null,
        ?ActionParametersGetter $actionParametersGetter = null,
        ?BranchNameValidator $branchNameValidator = null,
        ?CommitNameValidator $commitNameValidator = null
    ) {
        $this->currentBranchNameGetter = $currentBranchNameGetter ?? new CurrentBranchNameGetter();
        $this->actionParametersGetter = $actionParametersGetter ?? new ActionParametersGetter();
        $this->branchNameValidator = $branchNameValidator;
        $this->commitNameValidator = $commitNameValidator;
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
            $this->actionParametersGetter->__invoke($action, 'branchPattern')
        );
        $commitNameValidator = $this->commitNameValidator ?? new CommitNameValidator(
            $commitMessage,
            $this->actionParametersGetter->__invoke($action, 'commitPattern')
        );

        $branchNameValidator->validate();
        $commitNameValidator->validate();
        $io->write(self::GREEN . 'Right commit format message ;)' . self::NC);
    }
}
