# Captain Hook PHP conventional commits on conventional branch checker

Captain hook conventional branch commits is just a 'hook' to check that a commit, in a conventional branching model, follows the conventional rules.

## Installation

Previously you have should installed captainhook in your project. See: https://github.com/captainhookphp/captainhook

Install this package using:

```bash
composer require fperezco/captainhook-conventional-branch-commits
```

After that, you should configure the commit-msg hook by doing:

```bash
php vendor/fperezco/captainhook-conventional-branch-commits/scripts/setup.php
```

That [command](https://raw.githubusercontent.com/fperezco/captainhook-conventional-branch-commits/refs/heads/main/php/scripts/setup.php) will download the script checker and install the commit-msg hook in captainhook.json.

## Usage

The commit checker script will be executed on every commit.

The branch should follow the conventional branches rules: https://conventional-branch.github.io/

And the commits the conventional commits rules: https://www.conventionalcommits.org/en/v1.0.0/

If the commit message does not follow the rules, the commit will be rejected.

For example:

In a branch named `'feature/ISSUE-856'`  the commit message should be: `'feat(ISSUE-856): add new feature' `


## Why a script file to check commit messages?

The code in this repository is a common solution for php and javascript projects. It works as source for a composer and npm package managers at the same time.

In php projects it works in conjuntion with [Captain Hook](https://github.com/captainhookphp/captainhook), and in javascript projects it works with [Husky](https://typicode.github.io/husky/).

Other solutions to maintain the same commit checker routine in both projects are welcome!


## Contributing

Pull requests are welcome. For major changes, please open an issue first
to discuss what you would like to change.


## License

[MIT](https://choosealicense.com/licenses/mit/)