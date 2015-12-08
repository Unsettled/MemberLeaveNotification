# Notifier

Need to make sure all access is revoked when a member leaves your corporation, but don't have the infrastructure to automate this? This application might be something for you. Once setup, it sends a message to a specified channel on Slack (be it private or public) with the name and time/date of the character that left.

## Requirements

* PHP 5.4 or newer
* Composer
* Slack
* A willing CEO

## Instructions

* Have your CEO create a limited, no-expiry API key with notifications
* Create a Slack token on https://api.slack.com/tokens
* Copy `src/Config.sample.php` to `src/Config.php` and edit the values
* Make sure your webserver/PHP user has write access to this folder, or create the `tmp` folder yourself:
  * `mkdir tmp`
  * `chown www-data: tmp/`
* Set up a cron job (or task on Windows) that runs the `check.php` script every so often (max once every 30 minutes.)
