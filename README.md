# Notifier

This application checks for members that have recently left your corporation.

## Requirements

* PHP 5.4 or newer
* A reasonable web server
* Slack
* A willing CEO

## Instructions

* Have your CEO create a limited, no-expiry API key with notifications
* Create a Slack token on https://api.slack.com/tokens
* Copy `Config.sample.php` in the `src` folder to `src/Config.php` and edit the values
* Make sure your webserver/PHP user has write access to this folder, or create the `tmp` folder yourself:
  * `mkdir tmp`
  * `chown www-data: tmp/`
* Set up a cronjob (or task on Windows) that runs the `check.php` script in the `pub` folder every so often (max once every 30 minutes.)
