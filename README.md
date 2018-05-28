# Extension Kit
 > It's an extension kit for our theme https://github.com/hieu-pv/nf-theme 
 
- [Installation](#installation)
- [Configuration](#configuration)
- [Compile asset file](#compiler)
- [Service](#service)
- [Working with local repository](#local-reposoitory)
- [Extension Configuration](#extension-configuration)

 
<a name="installation"></a>
#### Installation

<a name="local-reposoitory"></a>
## Add notifications folder in your theme
> Add notifications folder in your theme

## Working with local repository
To add namespace of package with PSR-4, add the following code to your composer.json then run command `composer dump-autoload` or `composer dump-autoload -o` to optimize

```
    "autoload": {
        "psr-4": {
            "Vicoders\\Notification\\": "notifications/src/"
        }
    },
```

##### Step 1: Install Through Composer
```
Updating ...
```

<a name="configuration"></a>
##### Step 2: Add the Service Provider
> Open `config/app.php` and register the required service provider.

```php
  'providers'  => [
        // .... Others providers 
        \Vicoders\Notification\NotificationServiceProvider::class
    ],
```

##### Step 3: Run migrate
> Run below command to migrate tables into database. It'll create 2 tables `wp_notify` and `wp_user_notify`.

```php
php command migrate
```

##### Step 4: Insert shortcodes
> Insert 2 shortcodes where you need

```php
[vc-notify link_more="#"]: Show icon notify, badge and link to redirect to list notify page
[vc-notify-list-page]: List all notifies
```

<!-- <a name="compiler"></a> -->

