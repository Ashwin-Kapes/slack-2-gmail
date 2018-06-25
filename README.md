
# Slack2Gmail

https://slack2gmail.cleverapps.io/

WIP

## Deploy on CleverCloud

### Environment variables

```
$ cat app/.env.docker
```

And set the following variables:

```
ENV=prod
APP_ENV=prod

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
```