# Telegram Bot Middleware Container

This is a container that allows you to create a Telegram Bot that communicates with various service APIs
and responds back in Telegram. The services are aimed at those services that can be selfhosted,
with APIs that can provide life with value.

This is a personal project which I know could be written better, but I needed something quick and dirty
to provide a better means of finding out information quickly. I will slowly be building on this to provide
more commands, better written code, and maybe even tests! The project is written in PHP as that is my preferred
choice of language.

## Services

The functionality of the services are limited by the API that it has to offer. We are at the mercy of the API
to work with these services. I tend to regularly update these services via my docker so you can assume it supports
the latest version of these services and older versions may not be supported. Your mileage may vary.

### [MonicaHQ](https://www.monicahq.com/)

This connects to your personal CRM.

Commands available:

* `/reminders` - To provide you with a list of all reminders.
* `/birthdays` - To provide you with a list of today's, belated, upcoming birthdays.

### [Grocy](https://grocy.info/)

This connects to the ERP for your Home.

Commands available:

* `/chores` - To provide you with a list of chores.


## Installation

You may run `php index.php` with the necessary environment variables in place but it is recommended to use
docker to get started.

### Telegram Bot Creation

Ensure you create your bot using [@BotFather](https://t.me/botfather).

Use the wizard to create commands based on the services you wish to connect.

Find your chat ID by using the [@IDBot](https://t.me/IDBot).
Add this bot to your group to the group's chat ID.

### Container

todo