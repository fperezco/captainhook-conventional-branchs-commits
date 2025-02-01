<?php

// Ensure that the script is executed from the project's root directory
$projectRoot = getcwd();

// Path to the captainhook.json file
$captainhookConfigPath = $projectRoot . '/captainhook.json';

// Path to the script file
$scriptFilePath = $projectRoot . '/vendor/fperezco/captainhook-conventional-branch-commits/scripts/check_conventional_commit_format.sh';

// ANSI escape codes for colors
define('COLOR_RESET', "\033[0m");
define('COLOR_GREEN', "\033[32m");
define('COLOR_RED', "\033[31m");
define('COLOR_YELLOW', "\033[33m");
define('COLOR_CYAN', "\033[36m");

// Function to output messages with colors
function coloredMessage($message, $color) {
    echo $color . $message . COLOR_RESET . "\n";
}

// Check if the captainhook.json file exists
if (file_exists($captainhookConfigPath)) {
    coloredMessage("captainhook.json already exists at the root of the project.", COLOR_CYAN);
} else {
    // If the file doesn't exist, notify the user and exit
    coloredMessage("captainhook.json file does not exist at the root of the project.", COLOR_RED);
    coloredMessage("Please make sure that the file is present before proceeding with the setup.", COLOR_RED);
    exit(1);  // Exit with a non-zero status to indicate failure
}

// Read the existing captainhook.json content
$captainhookJson = json_decode(file_get_contents($captainhookConfigPath), true);

// Check if 'commit-msg' exists and is not empty
if (isset($captainhookJson['commit-msg']) && !empty($captainhookJson['commit-msg']['actions'])) {
    coloredMessage("'commit-msg' hook configuration already exists.", COLOR_YELLOW);

    // Ask the user if they want to replace the existing configuration
    $response = readline("Do you want to replace it? (yes/no): ");

    // If the user doesn't want to replace the configuration, exit the script
    if (strtolower($response) !== 'yes') {
        coloredMessage("Configuration was not replaced.", COLOR_GREEN);
        exit(0);
    }

    coloredMessage("Replacing the existing 'commit-msg' configuration...", COLOR_GREEN);
} else {
    coloredMessage("Adding 'commit-msg' hook configuration...", COLOR_GREEN);
}

// Add or update the commit-msg hook configuration
$captainhookJson['commit-msg'] = [
    'enabled' => true,
    'actions' => [
        [
            'action' => '\\fperezco\\CaptainhookConventionalBranchCommits\\CheckConventionalScriptExecutorGateway',
            'options' => []
        ]
    ]
];

// Save the updated configuration back to captainhook.json
file_put_contents($captainhookConfigPath, json_encode($captainhookJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

coloredMessage("'commit-msg' hook configuration added/updated successfully.", COLOR_GREEN);
coloredMessage("Setup completed.", COLOR_GREEN);
