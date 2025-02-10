# Captain Hook PHP conventional commits on conventional branch checker

Captain hook conventional branch commits is just a 'hook' to check that a commit, in a conventional branching model, follows the conventional rules.

## Installation

Previously you have should installed captainhook in your project. See: https://github.com/captainhookphp/captainhook

Install this package using:

```bash
composer require --dev fperezco/captainhook-conventional-branchs-commits
```

After that, you should configure the commit-msg hook by doing:

```bash
vendor/bin/captainhook-conventional-branchs-commits configure
```

That command will install the selected config in the commit-msg hook in captainhook.json.
By default( option 1), empty configuration so only check for standard conventional commits and branches will be installed.

## Configuration

The available variables in the commit-msg hook section for this validator are:

                
    "branchPattern": "/^(develop|master|main|(feature|bugfix|hotfix|chore|release)\\/\\w+(-.*)?)$/",
    "commitPattern": "/^(customcommit|patterncommit): [A-Za-z0-9-]+$/",

    "branchShouldIncludeTicketCode": true,
    "branchTicketCodePattern": "/^([A-Z]+-[0-9]+)$/",

    "commitShouldIncludeTicketCode": true,
    "commitTicketCodePattern": "/^([A-Z]+-[0-9]+)$/",

    "commitAndBranchShouldIncludeTheSameTicketCode": true,
    "commitAndBranchCommonTicketCodePattern": "/^([A-Z]+-[0-9]+)$/",
    "commitAndBranchCommonTicketCodePatternBranchExceptionsPattern": "/^(develop|master|main)$/"
                

The main variables are those to define the branchPattern and commitPattern.

The pattern that the branch should follow. If no provided, the default is the conventional branches pattern:
```
"branchPattern": '/^(develop|master|main|(feature|bugfix|hotfix|chore|release)\/[A-Za-z0-9-]+)$/'
```

The pattern that the commit should follow. If no provided, the default is the conventional commits pattern (in this case skipping Merge commits):
```
commitPattern: /^(Merge.*|(feat|fix|build|chore|ci|docs|style|refactor|perf|test)(\([A-Za-z0-9-]+\))?: .+)/'
```



If your project requires to provide a specific ticket code always you can set this section, by default/not defined the check is disabled:
```
"branchShouldIncludeTicketCode": true,
"branchTicketCodePattern": "/^([A-Z]+-[0-9]+)$/",
```

If your project requires to provide a specific ticket code in the commit message, you can set this section, by default/not defined the check is disabled:
```
"commitShouldIncludeTicketCode": true,
"commitTicketCodePattern": "/^([A-Z]+-[0-9]+)$/",
```

If your project requires that the commit and branch should have the same ticket code in the branch name and in the commit message , you can set this section, by default/not defined the check is disabled:
```
"commitAndBranchShouldIncludeTheSameTicketCode": true,
"commitAndBranchCommonTicketCodePattern": "/[A-Z]+-[0-9]+/",
"commitAndBranchCommonTicketCodePatternBranchExceptionsPattern": "/^(develop|master|main)$/"
```


## Usage

The conventional branch and commit checker script will be executed on every commit.

By default, the branch should follow the conventional branches rules: https://conventional-branch.github.io/

And, by default, the commits the conventional commits rules: https://www.conventionalcommits.org/en/v1.0.0/

If the commit message does not follow the rules, the commit will be rejected.



## Contributing

Pull requests are welcome. For major changes, please open an issue first
to discuss what you would like to change.

## License

[MIT](https://choosealicense.com/licenses/mit/)