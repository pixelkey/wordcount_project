## Syncing with a Template Repository

### 1. Add Template Repository as a Remote:
```shell
git remote add template git@github.com:pixelkey/docker-apache-wordpress.git
```

### 2. Fetch the Changes:
```shell
git fetch --all
```

### 3. Merge the Changes:
```shell
git merge template/[branch to merge] --allow-unrelated-histories
```

---

### Cherry-Pick Method:

#### 1. Cherry-Pick the Changes:
- First, identify the specific commit(s) in the template repository that contain the core setup files you want to sync.
```shell
git cherry-pick <commit-hash>
```

#### 2. Commit the Changes
```shell
git commit -m "Sync core setup files from template repository"
```

Make sure to replace `[URL of the template repo]` and `<commit-hash>` with the actual URL and commit hash that you intend to use.

### Troubleshooting ###
It appears that a database connection issue can be caused by the "data" folder (in the project root) not having owner and group set to root. See docker/cli folder for scripts to help resolve the issue. Also, sometimes, especially if you have refreshed the database, it may take 5 to 10 minutes to build and will show this error until it is available.