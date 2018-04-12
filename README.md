Chat for Kanboard
=================

[![Build Status](https://travis-ci.org/kanboard/plugin-chat.svg?branch=master)](https://travis-ci.org/kanboard/plugin-chat)

Minimalist internal chat for Kanboard.

- Only one room for all users (small team)
- No one to one chat
- Notification on user mention
- Simplified Markdown rendering
- History of 50 visible messages
- Highlight unread messages
- 3 different views: minimized, normal and maximized
- Auto-flush old messages in database to avoid large table

This is a very basic and minimalist chat extension.

The goal is **NOT** to replace full-featured traditional chat applications.
If you need something more elaborated, you should probably use Slack, Mattermost, RocketChat, Jabber or IRC.

Screenshots
-----------

### Normal view

You can discuss with people from a small window at the bottom left:

![Normal view](https://cloud.githubusercontent.com/assets/323546/23592581/302b0d5e-01d1-11e7-96bd-ac1ff15ef0cd.png)

### Maximized View

If you would like to see more messages, you can enlarge the window:

![Maximized view](https://cloud.githubusercontent.com/assets/323546/23592555/d6f51e3c-01d0-11e7-97f7-6bc8cd3c996d.png)

### Minimized View

You can minimize the chat window if needed:

![Minimized view](https://cloud.githubusercontent.com/assets/323546/23592397/3775644a-01ce-11e7-8f03-a16d9f953dc9.png)

### Notification when minimized

If someone mention you, the chat will blink discreetly:

![Notification](https://cloud.githubusercontent.com/assets/323546/23592372/d375f842-01cd-11e7-8730-361fa8ed8f3e.gif)

Author
------

- Frédéric Guillot
- License MIT

Requirements
------------

- Kanboard >= 1.2.3

Installation
------------

You have the choice between 3 methods:

1. Install the plugin from the Kanboard plugin manager in one click
2. Download the zip file and decompress everything under the directory `plugins/Chat`
3. Clone this repository into the folder `plugins/Chat`

Note: Plugin folder is case-sensitive.

Configuration
-------------

From the application settings, you can adjust the chat settings:

![Settings](https://cloud.githubusercontent.com/assets/323546/23592607/956f8e88-01d1-11e7-8cbc-2c0b269fef9f.png)
