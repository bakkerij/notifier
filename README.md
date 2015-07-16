# Notifier plugin for CakePHP

> Note: This is a non-stable plugin for CakePHP 3.x at this time. It is currently under development and should be considered experimental.

the Notifier plugin for CakePHP 3.x (and the [CakeManager](https://github.com/cakemanager/cakephp-cakemanager) is able
to create notifications very easily. Also the plugin helps you getting a list of notifications for your users.

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
    composer require cakemanager/cakephp-notifier
```

## Configurations

You will need to add the following line to your application's bootstrap.php file:

    Plugin::load('Notifier', ['bootstrap' => true, 'routes' => true]);

    // or run the following command:
    bin/cake plugin install -b -r Notifier

Note: You don't need to load the routes if you are not using the CakeManager Plugin.

After loading the plugin you need to migrate the tables for the plugin using:

    bin/cake migrations migrate -p Notifier

## Usage

The `NotifierComponent` is the most important part of the plugin. this Component is able to register templates, send
new notifications, and return a list of unread notifications.

Add the following to your AppController:

    $this->loadComponent('Notifier.Notifier');

### Templates
Notifications are viewed in a template including variables. When sending a new notification, you tell the notification
what template to use.

An example about how to add templates:

    $this->Notifier->addTemplate('newBlog', [
        'title' => 'New blog by :username',
        'body' => ':username has posted a new blog named :name'
    ]);

When adding a new template, you have to add a `title` and a `body`. Both are able to contain variables like `:username`
and `:name`. Later on we will tell more about these variables.

Removing a template is easy:

    $this->Notifier->removeTemplate('newBlog');

### Notify
Now we will be able to send a new notification using our `newBlog` template.

    $this->Notifier->notify([
        'users' => [1,2],
        'roles' => [1],
        'template' => 'newBlog',
        'vars' => [
            'username' => 'Bob Mulder',
            'name' => 'My great new blogpost'
        ]
    ]);

With the `notify` method we sent a new notification. A list of all attributes:

- `users` - This is an integer or array filled with id's of users to notify. So, when you want to notify user 261 and 373, add
`[261, 373]`.
- `roles` - Sometimes you want to notify a whole role (notify all administrators when there's a new user). For that you
can use the `roles` attribute. This can be a integer or array.
- `template` - The template you added, for example `newBlog`.
- `vars` - Variables to use. In the template `newBlog` we used the variables `username` and `name`. These variables can
be defined here.

### NotificationManager
When you are in a controller, you can use the `NotifierComponent` to use the `notifiy`-method. On all other locations in
CakePHP you can use the `NotificationManager`. This class is a singleton, and can be called this way:

    // call the Manager
    NotificationManager::instance()
    
    // notify
    NotificationManager::instance()->notify([
        'users' => [1,2],
        'roles' => [1],
        'template' => 'newBlog',
        'vars' => [
            'username' => 'Bob Mulder',
            'name' => 'My great new blogpost'
        ]
    ]);
    
The `NotificationManager` has the following namespace: `Notifier\Utility`.

### Lists
Of course you want to get a list of notifications per user. Here are some examples:

    // getting a list of notifications of the current logged in users
    $this->Notifier->notificationList();

    // getting a list of unread notifications of the user with id 2
    $this->Notifier->notificationList(2);
    
    // getting a list of all notifications
    $this->Notifier->allNotificationList();
    
    // getting an integer how many unread notifications the current logged in users has
    $this->Notifier->notificationCount();

    // getting an integer how many unread notifications the user with id 2 has
    $this->Notifier->notificationCount(2);
    
    // getting an integer how many  notifications the current logged in users has
    $this->Notifier->allNotificationCount();

You can do something like this to use the notification-list in your view:

    $this->set('notifications', $this->Notifier->notificationList());
    
### Model / Entity
The following getters can be used at your entity:
- `title` - The generated title including the variables.
- `body` - The generated body including the variables.
- `unread` - Boolean if the notification is not read yet.
- `read` - Boolean if the notification is read yet.

Example:
    
    // returns true or false
    $entity->get('unread');
    
    // returns string
    $entity->get('body');
