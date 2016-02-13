Workflow
===

Bpong will use a git flow style of development.

The style of workflow can be viewed @ https://www.atlassian.com/git/tutorials/comparing-workflows/gitflow-workflow

This will allow us to handle multiple developers at one time and track their changes responsibly and remain flexible to changing needs.

It is also suggested that we use https://github.com/nvie/gitflow which is based on http://nvie.com/posts/a-successful-git-branching-model/

This is a best practice used in today's industry. Even though there are others that work as well, this is the one that Bpong will use.

Installing the gitflow module into a forked repository helps to take alot of the guess work out of what is being merged.

Once a developer forks the development branch of First Gen, they should `git flow init` inside of their repository to initialize their local copy.

From there, they can create new branches to work on and do a pull request against the branches to the main repository.

In this way we can also mitigate the chances of bugs and issues being introduced into the code.
