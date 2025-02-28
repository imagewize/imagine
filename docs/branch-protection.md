# Branch Protection Setup

To enforce code quality and prevent direct pushes to the main branch, follow these steps to set up branch protection:

1. Go to your GitHub repository
2. Click on "Settings" tab
3. Navigate to "Branches" in the left sidebar
4. Under "Branch protection rules", click "Add rule"
5. Configure the following settings:

## Branch name pattern
```
main
```

## Protection settings to enable:
- ✅ Require a pull request before merging
  - ✅ Require approvals (at least 1)
- ✅ Require status checks to pass before merging
  - ✅ Require branches to be up to date before merging
  - Status checks that are required:
    - ✅ build (the CI job that runs on PRs)
- ✅ Include administrators (applies rules to everyone)

This configuration ensures:
1. No one can push directly to main
2. All changes must be made via Pull Requests
3. PRs must have at least one review approval
4. PR must pass the CI build job
5. Branch must be up-to-date with main before merging

This workflow improves code quality by enforcing peer review and automated testing.
