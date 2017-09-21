Jaxon Library for Symfony
=========================

This package integrates the [Jaxon library](https://github.com/jaxon-php/jaxon-core) into the Symfony framework.

Features
--------

- Automatically register Jaxon classes from a preset directory.
- Read Jaxon options from a config file.

Installation
------------

Add the following lines in the `composer.json` file, and run the `composer update` command.
```json
"require": {
    "jaxon-php/jaxon-symfony": "~2.0"
}
```

Declare the Jaxon bundle in the `app/AppKernel.php` file.
```php
$bundles = array(
    ...
    new Jaxon\AjaxBundle\JaxonAjaxBundle(),
);
```

Setup the default routing for Jaxon request by adding the following line in the `app/config/routing.yml` config file.
```yaml
jaxon_ajax:
    resource: "@JaxonAjaxBundle/Resources/config/routing.yml"
    prefix:   /
```

Import the service definition and configuration file of the Jaxon bundle in the `app/config/config.yml` config file.
```yaml
imports:
    ...
    - { resource: jaxon.yml }
    - { resource: "@JaxonAjaxBundle/Resources/config/services.yml" }
```

Create and edit the `app/config/jaxon.yml` file to suit the needs of your application.
A sample config file is available online at [the examples repo](https://github.com/jaxon-php/jaxon-examples/blob/master/frameworks/symfony/app/config/jaxon.yml).

Configuration
------------

The settings in the `app/config/jaxon.yml` config file are separated into two sections.
The options in the `lib` section are those of the Jaxon core library, while the options in the `app` sections are those of the Symfony application.

The following options can be defined in the `app` section of the config file.

| Name | Description |
|------|---------------|
| classes | An array of directory containing Jaxon application classes |
| views   | An array of directory containing Jaxon application views |
| | | |

By default, the `views` array is empty. Views are rendered from the framework default location.
There's a single entry in the `classes` array with the following values.

| Name | Default value | Description |
|------|---------------|-------------|
| directory | jaxon/Classes | The directory of the Jaxon classes |
| namespace | \Jaxon\App  | The namespace of the Jaxon classes |
| separator | .           | The separator in Jaxon class names |
| protected | empty array | Prevent Jaxon from exporting some methods |
| | | |

Usage
-----

This is an example of a Symfony controller using the Jaxon library.
```php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DemoController extends Controller
{
    public function indexAction(Request $request)
    {
        // Register the Jaxon classes
        $jaxon = $this->get('jaxon.ajax');
        $jaxon->register();
        // Print the page
        return $this->render('demo/index.html.twig',
            'jaxon_css' => $jaxon->css(),
            'jaxon_js' => $jaxon->js(),
            'jaxon_script' => $jaxon->script()
        ]);
    }
}
```

Before it prints the page, the controller makes a call to `$jaxon->register()` to export the Jaxon classes.
Then it calls the `$jaxon->css()`, `$jaxon->js()` and `$jaxon->script()` functions to get the CSS and javascript codes generated by Jaxon, which it inserts into the page.

### The Jaxon classes

The Jaxon classes must inherit from `\Jaxon\Sentry\Armada`.
By default, they are located in the `jaxon/Classes` dir of the Symfony application, and the associated namespace is `\Jaxon\App`.

This is a simple example of a Jaxon class, defined in the `jaxon/Classes/HelloWorld.php` file.

```php
namespace Jaxon\App;

class HelloWorld extends \Jaxon\Sentry\Armada
{
    public function sayHello()
    {
        $this->response->assign('div2', 'innerHTML', 'Hello World!');
        return $this->response;
    }
}
```

Check the [jaxon-examples](https://github.com/jaxon-php/jaxon-examples/tree/master/frameworks/symfony) package for more examples.

### Request processing

By default, the Jaxon request are handled by the controller in the `src/Controllers/JaxonController.php` file.
The `/jaxon` route is defined in the `src/Resources/config/routing.yml` file, and linked to the `JaxonController::indexAction()` method.

The request processing can be customized by extending the default controller and overloading the following method.

- `public function initInstance($instance)`: called for any Jaxon class instanciated.
- `public function beforeRequest($instance, $method, &$bEndRequest)`: called before processing the request.
- `public function afterRequest($instance, $method)`: called after processing the request.

See [https://www.jaxon-php.org/docs/armada/bootstrap.html]() for more information about processing callbacks.

Contribute
----------

- Issue Tracker: github.com/jaxon-php/jaxon-symfony/issues
- Source Code: github.com/jaxon-php/jaxon-symfony

License
-------

The package is licensed under the BSD license.
