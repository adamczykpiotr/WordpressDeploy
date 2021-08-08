# WordpressDeploy
Deploys Wordpress local file changes on remote server.\
Supports both development & production environments.

## Usage:

Default (dev) environment: ```php deploy.php```\
Development environment: ```php deploy.php dev``` or ```php deploy.php beta```\
Production environment: ```php deploy.php prod ``` or ```php deploy.php production ```


### Console output:
```
F:\WordpressDeploy>php deploy.php
[Environment]
No environment specified, DEV assumed.
[Pre-deploy actions]
[===============================] 100%  2/2 remaining: 0 sec.  elapsed: 1 sec.
[Creating archive]
[===============================] 100%  1030/1030 remaining: 0 sec.  elapsed: 2 sec.
[Creating deployment script]
[===============================] 100%  8/8 remaining: 0 sec.  elapsed: 0 sec.
[Uploading files]
[===============================] 100%  4/4 remaining: 0 sec.  elapsed: 7 sec.
[Executing deployment script]
[===============================] 100%  4/4 remaining: 0 sec.  elapsed: 0 sec.

Deployment successfully finished at 2021-08-08 12:34:36 in 15.4 seconds

[Post-deploy actions]
[===============================] 100%  1/1 remaining: 0 sec.  elapsed: 0 sec.

F:\WordpressDeploy>
```

### Additional confirmation is required while deploying on production
```
F:\WordpressDeploy>php deploy.php prod
[Environment]
Deploying on PRODUCTION environment. Continue? [y/n]: y
...
```
