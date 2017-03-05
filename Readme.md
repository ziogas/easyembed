Easy Embed
=========

About
---
EasyEmbed ( EE ) is a micro framework optimized for embeding it into already coded custom enviroments and systems.

Support for full MVC (models/views/controllers), autoloader, lightweight router, global variables, configuration system, database agnostic. Everything under ~500 lines of code.
Extendable with built in plugins for session handling, translations, database access and validation.

The **primary objectives** of EE are to ensure flexible, ligtweight/high performance and **portable way to design mini-apps inside another projects**. However it can be used as a stand-alone application too.

If you stuck on old project but want to have some custom functionality without digging into code or make mini-app that can be used everywhere then EE is just for you.

Why
---
EE is not intended to be next generation framework. However It is **designed to support old (PHP < 5.3)** systems where you can try new stuff and code new features unrelated to existing codebase while still keeping some relations. Although EE supports PHP >= 5.1.3 It's working well on latest PHP versions too.

If you want fully featured and also high performance PHP framework consider [Symfony 2], [Codeigniter], [Phalcon] and [XtFramework]. If you're looking for a lightweight micro framework and have decent PHP >= 5.3 support consider [Laravel] or [Silex]. They do really good job in the new PHP namespacing fashion. 

Proposed structure
---
```sh
 * /.htaccess           <- Needed to ensure that all requests are made through index.php. If you use nginx just look at nginx.conf examples.
 * /index.php           <- This is the main bootstrap file which fires everything. Those 3 lines of code written here can be ported anywhere.
 * /config.php          <- Main config file. Whatever format and content you want, as long as it returns an array.
 * /routes.php          <- Main routes definitions. Used to define base path and main app routes.
 * /ee.php              <- Main framework file. All framework class methods live here.
 * /app/                <- Application directory. All the application related code should be here.
 *   /app/controllers/  <- Self explaining
 *   /app/models/       <- Self explaining
 *   /app/views/        <- Self explaining
 *   /app/assets/       <- Self explaining
 *   /app/tests/        <- Self explaining
 * /libs/               <- All shared libraries.
 * /tests/              <- Unit tests of framework.
 * /cli                 <- File for the all cli actions/helpers.
```

Requirements
---
* PHP >= 5.1.3
* PDO for database usage. 
* Apache mod rewrite or corresponding nginx rules setup
* Also if you want to use unit testing you'll need PHP >= 5.3.

---

Main framework parts
----
As this is in general one file framework you're encouraged to scan ee.php for better understanding.

**Initialization**

To initialize framework you just need to call init method
```php
EE::init();
```
This will load all the app configs and routes.

**Execution**

To load page call load_app method
```php
EE::load_app();
```
This will load and execute page.

All these actions are performed in index.php
```php
<?php
// Bootstrap file
  
// Require main framework class
require(dirname(__FILE__).'/ee.php');

// Initialize and load configs/routes
EE::init();
  
// Load and execute page
EE::load_page();
```

**Global variables**

EE have some specific methods to hold global variables. Those are accessed from EE::get, EE::get_ref and setted via EE::set methods. Those variables are also used when parsing views.

**Page loading**

When calling load_page method it requests routing function and either returns output either prints it directly.
Routes are defined with regular expressions help in app/routes.php file. Route config must point to controller and may be pointing to specific action (method). Some examples:

```php
'/another/:num' => array('controller' => 'test', 'action' => 'another'),
'/something' => array('controller' => 'test', 'action' => 'something'),
'/something/:any' => array('controller' => 'test', 'action' => 'something'),
'/custom/(.+?)-(.+?)' => array('controller' => 'test', 'action' => 'custom'),
'/:any' => array('controller' => 'test'),
```
All the controller actions must have "_action" suffix.

**Controllers**

All the app controllers should be on app/controllers directory. Their name and file must have "_controller" suffix. Framework calls them with EE::controller() method help. There are no other restrictions to controllers in general.

```php
<?php

/*
 * Sample test controller
 */
class test_controller
{
    public function __construct()
    {
        //$this->model = EE::model('test');
    }

    /*
     * This private method is just a way to always include layout and could be completely avoided.
     */
    private function load_view($page)
    {
        EE::set('__page', $page);
        return EE::view('layout');
    }

    public function index_action($path)
    {
        $this -> load_view('index');
    }
}
```

**Models**

All the app models should be on app/models directory. Their name and file must have "_model" suffix. Framework calls them with EE::model() method help. Almost everytime it's useful to define pdo object in model constructor 
```php
<?php

/*
 * Sample test model
 */
class test_model
{
    private $pdo;

    public function __construct ()
    {
        $this->pdo = db_pdo::load('main');
    }
}
```

There are no other restrictions to models in general.

**Views**

By default EE uses no 3rd party templating system and emphasis PHP itself as a templating engine. Therefore with the help of [Shorttags] and [Alternative syntax for control structures] it works nice by default. Views are rendered with EE::view() method by passing file and additional variables. Also in views are extracted all the global variables.

```php
public function foo_action($path)
{
    $id = (isset($path[0])) ? $path[0] : null;

    EE::set('id', $id);
    return EE::view('test');
}
```

All the app views should be on app/views directory. Their file must have "_view" suffix

**Assets**

Assets are every static content like css, js or images. By default they are in app/assets directory and Apache mod rewrite or nginx rules makes them accessible via /assets path.

**Libs**

All custom framework libraries are located in libs directory. Right now there are helpers for pdo, session initialization and locale translations.

**Tests**

Framework tests are located in tests directory and app tests should be on app/tests. They should be PHPUnit compatible and can be executed by cli helper. Just run 

```sh
./cli test_app
OR
./cli test app/tests/path_to_test.php
```
Framework tests can be invoked via test_framework helper.

**URL**

There are EE::url() method which prepends base_path for every url to work correctly.
```php
echo EE::url('assets/image.jpg');
echo EE::url('some-cool-action/123');
```

---

Installation
--------------

```sh
* Clone repository:
  git clone git://github.com/ziogas/easyembed.git ee
* Modify /ee/.htacess /ee/app/config.php and /ee/app/routes.php files according to your setup. Almost every time you need edit just base_path and database credentials.
* Run code in browser
```

Version
----
1.1

Author
----
Arminas Žukauskas &lt;arminas[eta]ini[dot]lt&gt;

License
----
[MIT]

[Codeigniter]:http://ellislab.com/codeigniter
[Symfony 2]:http://symfony.com/
[Phalcon]:http://phalconphp.com/en/
[XtFramework]:https://github.com/XtGem/XtFramework
[Laravel]:http://laravel.com/
[Silex]:http://silex.sensiolabs.org/
[Shorttags]:http://www.php.net/manual/en/language.basic-syntax.phptags.php
[Alternative syntax for control structures]:http://www.php.net/manual/en/control-structures.alternative-syntax.php
[MIT]:https://tldrlegal.com/license/mit-license
