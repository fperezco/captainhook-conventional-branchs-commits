<?php

namespace fperezco\CaptainhookConventionalBranchCommits\setup;

class Setup
{
    private const CONFIG_FILE = 'captainhook.json';

    private const COLOR_RESET = "\033[0m";
    private const COLOR_GREEN = "\033[32m";
    private const COLOR_RED = "\033[31m";
    private const COLOR_YELLOW = "\033[33m";
    private const COLOR_CYAN = "\033[36m";

    public static function run(): void
    {
        $projectRoot = getcwd();
        $configPath = $projectRoot . '/' . self::CONFIG_FILE;

        if (!file_exists($configPath)) {
            self::coloredMessage("Error: " . self::CONFIG_FILE . " not found.", self::COLOR_RED);
            self::coloredMessage(
                "Please install and configure Captain Hook (captainhook/captainhook) first.",
                self::COLOR_RED
            );
            exit(1);
        }

        $config = json_decode(file_get_contents($configPath), true);

        self::coloredMessage("Select the configuration to install:", self::COLOR_YELLOW);
        self::coloredMessage("1. Conventional branch and commit validator (default)", self::COLOR_CYAN);
        self::coloredMessage("2. Wata convention (JIRA ticket validation)", self::COLOR_CYAN);

        $choice = trim(readline("Enter the number corresponding to your choice: "));

        if ($choice === '2') {
            self::coloredMessage("Installing Wata convention...", self::COLOR_GREEN);
            $config['commit-msg'] = [
                'enabled' => true,
                'actions' => [
                    [
                        'action' => '\\fperezco\\CaptainhookConventionalBranchCommits\\CheckConventionalScriptExecutorGateway',
                        'options' => [
                            "commitAndBranchShouldIncludeTheSameTicketCode" => true,
                            "commitAndBranchCommonTicketCodePattern" => "/[A-Z]+-[0-9]+/",
                            "commitAndBranchCommonTicketCodePatternBranchExceptionsPattern" => "/^(develop|master|main)$/"
                        ]
                    ]
                ]
            ];
        } else {
            self::coloredMessage("Installing Conventional branch and commit validator...", self::COLOR_GREEN);
            $config['commit-msg'] = [
                'enabled' => true,
                'actions' => [
                    [
                        'action' => '\\fperezco\\CaptainhookConventionalBranchCommits\\CheckConventionalScriptExecutorGateway',
                        'options' => []
                    ]
                ]
            ];
        }

        file_put_contents($configPath, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        self::coloredMessage("'commit-msg' hook configuration added/updated successfully.", self::COLOR_GREEN);
        self::coloredMessage("Setup completed.", self::COLOR_GREEN);
    }

    private static function coloredMessage(string $message, string $color): void
    {
        echo $color . $message . self::COLOR_RESET . "\n";
    }
}