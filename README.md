[![Twitter](https://img.shields.io/twitter/url/http/shields.io.svg?style=social)](https://twitter.com/thinfactory)

# TeamLeader - Discord

This application reformats [TeamLeader](https://teamleader.eu/) webhook
events into [Discord](https://discordapp.com/) embeded cards.

## Requirements

This project requires the following globally installed applications:

* [php5-cgi](https://www.php.net/)

```
# apt-get install php-cgi
```

## Installation & Updates

This project requires the following locally installed applications:

* [Composer](https://getcomposer.org/)

Once the repository is checked out, initialize the project:

```
$ curl -sS https://getcomposer.org/installer | php
$ php composer.phar install
```

If you previously have performed the installation, you should use `update` to refresh any outdated or obsolete modules:

```
$ git pull
$ php composer.phar update
```

Use the example environment configuration to initialize the application:

```
$ cp .env.default .env
```

Edit the application configuration in `.env` and make sure the settings reflect
the environment you wish to deploy on.

## Webhooks

You will need to configure the [TeamLeader](https://teamleader.eu/) and [Discord](https://discordapp.com/)
webhooks in your environment configuration.

### TeamLeader

Information on how to use the TeamLeader API and webhooks:

 * [Knowledgebase Article](http://support.teamleader.eu/topics/204-where-can-i-find-more-information-on-the-possibilities-around-api-webhooks-in-teamleader/)
 * [API Documentation](http://apidocs.teamleader.be/)

### Discord

Information on how to use the Discord API and webhooks:

 * [Intro to Webhooks](https://support.discordapp.com/hc/en-us/articles/228383668-Intro-to-Webhooks/)
 * [Webhook API Documentation](https://discordapp.com/developers/docs/resources/webhook/)

## Event Routing

By default, all events are advertised on the `DISCORD_WEBHOOK` webhook. You can route specific events
to another webhook by adding the event into the environment configuration `DISCORD_WEBHOOK_EVENT_*`.

For example:

```
DISCORD_WEBHOOK_EVENT_SALE_ACCEPTED="DISCORD_WEBHOOK_EVENT_SALE_ACCEPTED"
```
