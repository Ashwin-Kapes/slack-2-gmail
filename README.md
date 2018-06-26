
# Slack2Gmail

https://slack2gmail.cleverapps.io/

WIP

## Deploy on CleverCloud

### Environment variables

```
$ cat app/.env.docker.dist
```

And set the following variables:

```
ENV=prod
APP_ENV=prod
APP_DEBUG=false
LOGGING_CHANNEL=syslog

# start app from app/ directory
APP_FOLDER=app
APP_URL=https://whatever.cleverapps.io

# php artisan key:generate
APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

ENABLE_METRICS=true

# create a Google project: https://console.developers.google.com/projectcreate
# enable Gmail and Google+ API
# create credentials here: https://console.developers.google.com/apis/credentials
GOOGLE_OAUTH2_CLIENT_ID=xxxxxxxxxxxxxxxxxxxxxxxxxxxx
GOOGLE_OAUTH2_CLIENT_SECRET=xxxxxxxxxxxxxxxxxxxxxxxxxxxx

# workers
CC_WORKER_COMMAND_0=cd app/ && php artisan slack:sync:orgs
CC_WORKER_COMMAND_1=cd app/ && php artisan slack:sync:channels
CC_WORKER_RESTART=always

# for persistant sessions after redeploy
CC_FS_BUCKET=/app/storage/framework/nfs-sessions:bucket-xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx-fsbucket.services.clever-cloud.com
NFS_SESSION_STORAGE=framework/nfs-sessions
```

### Workers

On CleverCloud, This is run is the same VM, thanks to CC_WORKERS_* environment variables.

```
$ php artisan slack:sync:orgs
$ php artisan slack:sync:channels
```

Run them only once!! Slack2Gmail does not support concurrent job executions.

## Contribute

```
$ docker-compose up -d
```

## Todo

- better error management
- transactional emails: welcome, nurturing...
- transactional emails to announce new channel in a slack org (or even channel deletion ?)
- export private messages
- display real name in @from email field
- export messages with attachment (and even files ?)
- insert gmail messages with thread ids (by channel)
- for app owner: Push notifications into Slack for new user, new org linked...
- for app owner: prometheus monitoring + alerting
