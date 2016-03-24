# Notifier plugin for CakePHP

[![Build Status](https://travis-ci.org/cakemanager/cakephp-notifier.svg?branch=master)](https://travis-ci.org/cakemanager/cakephp-notifier)
[![Coverage Status](https://coveralls.io/repos/cakemanager/cakephp-notifier/badge.svg?branch=master&service=github)](https://coveralls.io/github/cakemanager/cakephp-notifier?branch=master)
[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/cakemanager/cakephp-notifier?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
    composer require cakemanager/cakephp-notifier:dev-master
```

## Usage

### Configurations

You will need to add the following line to your application's bootstrap.php file:

    Plugin::load('Notifier', ['bootstrap' => true, 'routes' => true]);

    // or run the following command:
    bin/cake plugin install -b -r Notifier

Note: You don't need to load the routes if you are not using the CakeManager Plugin.

After loading the plugin you need to migrate the tables for the plugin using:

    bin/cake migrations migrate -p Notifier

### NotificationManager

The `NotificationManager` is the Manager of the plugin. You can get an instance with:

    NotificationManager::instance();

The `NotificationManager` has the following namespace: `Notifier\Utility\NotificationManager`.

### NotifierComponent

The `NotifierComponent can be used in controllers to create notifications and return data like read/unread notifications
and totals (like 4 unread notifications).

### Templates
Notifications are viewed in a template including variables. When sending a new notification, you tell the notification
what template to use.

An example about how to add templates:

    $notificationManager->addTemplate('newBlog', [
        'title' => 'New blog by :username',
        'body' => ':username has posted a new blog named :name'
    ]);

When adding a new template, you have to add a `title` and a `body`. Both are able to contain variables like `:username`
and `:name`. Later on we will tell more about these variables.

Removing a template is easy:

    $notificationManager->removeTemplate('newBlog');

### Notify
Now we will be able to send a new notification using our `newBlog` template.

    $notificationManager->notify([
        'users' => [1,2],
        'recipientLists' => ['administrators'],
        'template' => 'newBlog',
        'vars' => [
            'username' => 'Bob Mulder',
            'name' => 'My great new blogpost'
        ]
    ]);

> Note: You are also able to send notifications via the component: `$this->Notifier->notify()`.

With the `notify` method we sent a new notification. A list of all attributes:

- `users` - This is an integer or array filled with id's of users to notify. So, when you want to notify user 261 and
373, add `[261, 373]`.
- `recipientLists` - This is a string or array with lists of recipients. Further on you can find more about
RecipientLists.
- `template` - The template you added, for example `newBlog`.
- `vars` - Variables to use. In the template `newBlog` we used the variables `username` and `name`. These variables can
be defined here.

### Lists
Of course you want to get a list of notifications per user. Here are some examples:

    // getting a list of all notifications of the current Logged In user
    $this->Notifier->getNotifications();

    // getting a list of all notifications of the user with id 2
    $this->Notifier->getNotifications(2);
    
    // getting a list of all unread notifications for user with id 2
    $this->Notifier->getNotifications(2, true);

    // getting a list of all read notifications for a user with id 2
    $this->Notifier->getNotifications(2, false);

    // getting a limited list of all notifications for user with id 2
    $options = ['limit' => 5];
    $this->Notifier->getNotifications(2, null, $options); // Return 5 notifications

    // getting a limited list of all unread notifications for Logged In user
    $options = ['limit' => 5];
    $this->Notifier->getNotifications(null, true, $options); // Return 5 notifications

    // getting a limited list of all notifications for logged in User
    $options = ['limit' => 5];
    $this->Notifier->getNotifications(null, null, $options); // Return 5 notifications
    
    // getting a number of all notifications of the current logged in user
    $this->Notifier->countNotifications();

    // getting a number of all notifications of the user with id 2
    $this->Notifier->countNotifications(2);
    
    // getting a number of all unread notifications
    $this->Notifier->countNotificationList(2, true);

    // getting a number of all read notifications
    $this->Notifier->countNotificationList(2, false);

You can do something like this to use the notification-list in your view:

    $this->set('notifications', $this->Notifier->getNotifications());

### RecipientLists
To send notifications to large groups you are able to use RecipientLists.
You can register them with:

    $notificationManager->addRecipientList('administrators', [1,2,3,4]);
    
Now we have created a list of recipients called `administrators`.

This can be used later on when we send a new notification: 

    $notificationManager->notify([
        'recipientLists' => ['administrators'],
    ]);

Now, the users 1, 2, 3 and 4 will recieve a notification.

### Model / Entity
The following getters can be used at your entity:
- `title` - The generated title including the variables.
- `body` - The generated body including the variables.
- `unread` - Boolean if the notification is not read yet.
- `read` - Boolean if the notification is read yet.

Example:
    
    // returns true or false
    $entity->get('unread');
    
    // returns the full output 'Bob Mulder has posted a new blog named My Great New Post'
    $entity->get('body');

## Keep in touch
If you need some help or got ideas for this plugin, feel free to chat at
[Gitter](https://gitter.im/cakemanager/cakephp-notifier).

Pull Requests are always more than welcome!
