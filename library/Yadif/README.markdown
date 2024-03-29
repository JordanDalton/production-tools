Yadif - Yet Another Dependency Injection Framework

* Originally by Thomas McKelvey (https://github.com/tsmckelvey/yadif/tree)
* Fork by Benjamin Eberlei (https://github.com/beberlei/yadif/tree)

Inject dependencies via a very simple configuration mechanism.

Table of Contents
=============

- Basic Syntax
- Object Configuration
- Scope Config
- Setting non-object parameters
- Creating entities or value objects through Container
- Zend_Application Integration
- Zend Framework Front Controller Example
- Zend_Config Support
- Open Questions
- Instantiation with Factory methods
- Injecting Container Reference or Clones
- Using the Builder for a fluent configuration interface
- Modules
- Clearing Instances
- TODOs

1. Basic Syntax
=============

Take this constructor and setter-less class:

    class Foo
    {
    }

Creating a Yadif Container configured to create this class looks:

    $config = array('Foo' => array());
    $yadif  = Yadif_Container::create($config);
    $foo    = $yadif->getComponent('Foo');

2. Object Configuration
=============

This current fork has a slighty different configuration syntax than the original:

    class Foo
    {
        public function __construct($arg1, $arg2) { }

        public function setA($arg1) { }

        public function setB($arg2, $arg3) { }
    }

    $config = array(
        'Foo' => array(
            'class' => 'Foo',
            'arguments' => array('ConstructorArg1', 'ConstructorArg2'),
            'methods' => array(
                array(
                    'method' => 'setA',
                    'arguments' => array('Inject1'),
                ),
                array(
                    'method' => 'setB',
                    'arguments' => array('Inject2', 'Inject3'),
                ),
            ),
            'scope' => 'singleton',
        ),
        'ConstructorArg1' => array('class' => 'ConstructorArg1'),
        'ConstructorArg2' => array('class' => 'ConstructorArg2'),
        'Inject1'         => array('class' => 'Inject1'),
        'Inject2'         => array('class' => 'Inject2'),
        'Inject3'         => array('class' => 'Inject3'),
    );

    $yadif = new Yadif_Container($config);
    $foo   = $yadif->getComponent("Foo");

Would do internally:

    $foo   = new Foo($yadif->getComponent('ConstructorArg1'), $yadif->getComponent('ConstructorArg2'));
    $foo->setA($yadif->getComponent('Inject1'));
    $foo->setB($yadif->getComponent('Inject2'), $yadif->getComponent('Inject3'));

Now 'ConstructorArg1', 'ConstructorArg2', 'Inject1', 'Inject2' and 'Inject3' would
also have to be defined as classes to be constructed correctly.

3. Scope Config
=============

Currently there are two different scopes: 'singleton' and 'prototype'. The first
one enforces the creation of only one object of the given type. The second one
creates new objects on each call of getComponent().

4. Setting non-object parameters
=============

Non-object parameters are bound to methods and constructors in a PDO like binding syntax.
For any non-oject parameter the syntax "double-colon name" has to be used to indicate
the parameter as non-object parameter.

As an example we take this Class:

    class Foo {
        public function __construct($bar, $number) { }

        public function setNumber($number) { }
    }

There are different scopes for these bounded parameters. A global scope and a
method aware only scope.

i.) Global Scope

Via Yadif_Container::bindParam() it is possible to set global parameters to a container.

    $config = array(
        'Foo' => array(
            'class' => 'Foo',
            'arguments' => array(':bar', ':number'),
        ),
    );

    $yadif = new Yadif_Container($config);
    $yadif->bindParam(':bar', 'BarName');
    $yadif->bindParam(':number', 1234);

    $foo = $yadif->getComponent('Foo');

ii.) Method aware scope

Via the configuration mechanism you can bind parameters to a specific method where the parameters occour.
An example for the object constructor looks like this:

    $config = array(
        'Foo' => array(
            'class' => 'Foo',
            'arguments' => array(':bar', ':number'),
            'params' => array(':bar' => 'BarName', ':number' => 1234),
        ),
    );

    $yadif = new Yadif_Container($config);
    $foo   = $yadif->getComponent('Foo');

For a non-object parameter in a method the syntax looks like this:

    $config = array(
        'Foo' => array(
            'class' => 'Foo',
            'arguments' => array(':bar', ':number'),
            'params' => array(':bar' => 'BarName', ':number' => 1234),
            'methods' => array(
                array(
                    'method' => 'setNumber',
                    'arguments' => array(':number'),
                    'params' => array(':number' => 1138),
                ),
            ),
        ),
    );

    $yadif = new Yadif_Container($config);
    $foo   = $yadif->getComponent('Foo');

5. Creating entities or value objects through Container
=============

The creation of entity or value objects mostly requires lots of arguments passed to the constructor
or setter methods, paired with dependencies for example in an Active Record example with the Database Connection.

    class UserActiveRecord
    {
        public function setAdapter($db) { }

        public function setName($name) { }

        public function setEmail($email) { }

        public function setId($id) { }
    }

    $config = array(
        'DatabaseConn' => array(
            'class' => 'PDO',
            'arguments' => array('mysql://localhost', 'username', 'password'),
            'scope' => 'singleton',
        ),
        'User' => array(
            'class' => 'UserActiveRecord',
            'arguments' => array(),
            'methods' => array(
                array('method' => 'setAdapter', 'arguments' => array('DatabaseConn'),
                array('method' => 'setName',    'arguments' => ':name'),
                array('method' => 'setEmail',   'arguments' => ':email'),
                array('method' => 'setId',      'arguments' => ':id'),
            ),
            'scope' => 'prototype' // instantiate new object on each call of getComponent()
        ),
    );

    $yadif = new Yadif_Container($config);
    $db    = $yadif->getComponent('DatabaseConn');
    $stmt = $db->query("SELECT * FROM Users");

    $users = array();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $user = $yadif->bindParam(':name', $row['name'])
                      ->bindParam(':id',   $row['id'])
                      ->bindParam(':email', $row['email'])
                      ->getComponent('User');
    }

You could also use:

    $user = $yadif->bindParams(array(':name' => $row['name'], ':id' => $row['id'], ':email' => $row['email']))->getComponent('User');

6. Zend_Application Integration
=============

With Zend Framework 1.8 the new Application component is sort of the dependency creation method for MVC (or any other)
application. Internally it uses a Container based on "Zend_Registry" by default and uses the __isset, __set and __get
magic methods to check, set and get instantiated resources. This Container is injected into the Front Controller and
accessible inside the Action Controller by:

    $container = $this->getInvokeArg('bootstrap')->getContainer();

This Container can be replaced by the Yadif_Container and be a hybrid of instances injected by Zend_Application Resources
(via their resource name) and configured components that can be lazy loaded from the Yadif DI Container. You can
switch the Zend_Registry with Yadif_Container by calling:

    $objects = new Zend_Config_Xml(APPLICATION_PATH."/config/objects.xml");
    $container = new Yadif_Container($objects);

    // Create application, bootstrap, and run
    $application = new Zend_Application(
        APPLICATION_ENV,
        APPLICATION_PATH . '/config/application.xml'
    );
    $application->getBootstrap()->setContainer($container);
    $application->bootstrap()->run();

How are resources injected into the container? Take for an example the "Db" resource.
If you are using it inside your Zend_Application configuration it will be set as the key:

    $container->db;

All resource names are "strtolower"'ed upon setting them into the container, aswell
as the retrieval keys, so you don't have to think about case-sensitivy when retrieving
the components from the container.

7. Zend Framework Front Controller Example
=============

    $config = array(
        'Request' => array(
            'class' => 'Zend_Controller_Request_Http',
        ),
        'Response' => array(
            'class' => 'Zend_Controller_Response_Http',
        ),
        'Router' => array(
            'class' => 'Zend_Controller_Router_Rewrite',
            'methods' => array(
                array('method' => 'addConfig', 'arguments' => array('RouterConfig', 'routes')),
            ),
        ),
        'RouterConfig' => array(
            'class' => 'Zend_Config_Ini',
            'arguments' => array(':routerConfigFile'),
            'params' => array(':routerConfigFile' => '/var/www/application/config/routes.ini'),
        ),
        'FrontController' => array(
            'class' => 'Zend_Controller_Front',
            'arguments' => array(),
            'methods' => array(
                // Inject Request, Response and Router
                array('method' => 'setRequest',  'arguments' => array('Request')),
                array('method' => 'setResponse', 'arguments' => array('Response')),
                array('method' => 'setRouter',   'arguments' => array('Router')),

                // Inject Plugins
                array('method' => 'registerPlugin', 'arguments' => array('ControllerPluginAccessControl')),
                array('method' => 'registerPlugin', 'arguments' => array('ControllerPluginLogging')),

                // Inject Parameters which will be used throughout controllers
                array('method' => 'setParam', 'arguments' => array('db', 'DatabaseConn')),
                array('method' => 'setParam', 'arguments' => array('logger', 'Logger')),

                // Set Controller Directory
                array(
                    'method' => 'setControllerDirectory',
                    'arguments' => array(':controllerDirectory')
                    'params' => array(':controllerDirectory' => '/var/www/application/controllers/'),
                ),
            ),
            'factory' => array('Zend_Controller_Front', 'getInstance'),
        ),
        'ControllerPluginAccessControl' => array('class' => 'ControllerPluginAccessControlImplementation'),
        'ControllerPluginLogging'       => array(
            'class' => 'ControllerPluginAccessControlLogging',
            'arguments' => array('Logger'),
        ),
        'Logger' => array(
            'class' => 'Zend_Log',
            'methods' => array(
                array('method' => 'addWriter', 'arguments' => array('LoggerWriter')),
            ),
        ),
        'LoggerWriter' => array(
            'class' => 'Zend_Log_Writer_Stream',
            'arguments' => array(':loggerStream'),
            'params' => array(':loggerStream' => '/var/log/myapplication/errors.log'),
        ),
        'DatabaseConn' => array(
            'class' => 'Zend_Db_Adapter_Mysql',
            'arguments' => array( '%database.config%'),
        ),
    );

    $yadif = new Yadif_Container($config);
    $front = $yadif->getComponent('FrontController');
    $front->dispatch();

    Will do the following internally:

    $front = Zend_Controller_Front::getInstance(); // because 'factory' config is set for this one

    $request = new Zend_Controller_Request_Http();
    $front->setRequest($request);

    $response = new Zend_Controller_Response_Http();
    $front->setResponse($response);

    $routerConfig = new Zend_Config('/var/www/application/config/routes.ini');
    $router = new Zend_Controller_Router_Rewrite();
    $router->addConfig($routerConfig);
    $front->addRouter($router);

    $aclPlugin = new ControllerPluginAccessControlImplementation();
    $front->registerPlugin($aclPlugin);

    $logger = new Zend_Log();
    $writer = new Zend_Log_Writer_Stream('/var/log/myapplication/errors.log');
    $logger->addWriter($writer);
    $loggerPlugin = new ControllerPluginAccessControlLogging($logger);
    $front->registerPlugin($loggerPlugin);

    $db = new Zend_Db_Adapter_Mysql('pdo_mysql', array(..));
    $front->setParam('db', $db);

    // $logger already exists and is singleton
    $front->setParam('logger', $logger);

    $front->setControllerDirectory('/var/www/application/controllers/');

8. Zend_Config Support
=============

You can use Zend_config objects as the primary configuration mechanism:

    $config = new Zend_Config_Xml("objects.xml");
    $yadif  = new Yadif_Container($config);

Also you can use specific arguments marked with %arg% to replace with values
inside a given application config object.

    $components = array(
        'YadifBaz' => array(
            'class' => 'YadifBaz',
            'methods' => array(
                array('method' => 'setA', 'arguments' => array('%foo.bar%'))
            ),
        ),
    );
    $config = new Zend_Config(array('foo' => array('bar' => 'baz')));

    $yadif = new Yadif_Container($components, $config);
    $baz = $yadif->getComponent("YadifBaz");

9. FAQ
=============

1. String and Component Name Ambigoutiy - How to solve it?
Solved! Allow non-object parameters only through 'arguments' key of configuration and bound via ':name' syntax.
See point 4 with more details on this topic.

2. Components Nested in Arrays, should they be instantiated through the Container? Currently they are not:

    $foo = new Foo(array('bar' => new Bar())); // <- not possible

Solved! Through introduction of the injectParameters() method it is possible to implement this through recursion and
even support both object and non-object parameters in the nested array.

3. Calling static registries for example not possible:

    Zend_Registry::set('foo', $foo);
    Zend_Controller_Action_HelperStack::addHelper($helper);

4. How to solve references to the container itsself? Example: The Front Controller is using a container to instantiate
the main part of the application and then goes on in the Dispatcher to use another container to instantiate the controller.
How should back-references to containers be injected into an object tree?

    $container = $container->getComponent('ThisContainer'); <- magic key?

5. Session Scope: Add a third parameter to the Container which accepts a Zend_Session_Namespace. All objects inside this
namespace are added to the list of instances where the session key corresponds to the Component Name. Instantiated objects
that are scoped "session" are saved inside the session, not just the container.

6. Currently no loading of the Classes through Zend_Loader::loadClass() - How to handle this?

7. Known Contexts
There are known contexts in which a container already knows he has to have implementations of x,y,z
different components. For example a Zend_Front_Controller at least needs request, response, router.

How should the configuration of known contexts be handled. Maybe a class "MyContext extends Yadif_Container"
but in this case the addComponent() functionality has to be extended to allow for a convention over configuration
compatible merging of existing with added component definitions.

10. Instantiation with Factory methods
=============

Sometimes you have to create certain objects through a factory method or a singleton
creation facility. You can do that with a specific factory key:

    $options = array(
        'Foo' => array(
            'class' => 'Foo',
            'factory' => array('FooFactory', 'create'),
        ),
    );

The factory key has to hold a valid PHP callback. Then not the constructor, but
the factory method is called to create the object. No check is performed if the object
is created successfully.

11. Injecting Container Reference or Clones
=============

Using 'ThisContainer' or 'CloneContainer' creates a reference to the current container
or clones the container and injects it.

12. Using the Builder for a fluent configuration interface
=============

At some point you end up with a huge XML or PHP Array configuration for all your
dependency injection requirements, which may be unmanagable and annoying to work with.

The "Yadif_Builder" class offers a fluent interface to create a Yadif compatible component
configuration. This gives a more explicit mechanism to create the configuration, which
can also be type-hinted in any IDE and therefore might offer more security when
working with the configuration.

    $builder = new Yadif_Builder();
    $builder->bind("InterfaceName")
            ->to("Implementation")
            ->args("ConstructorDependencyA", "ConstructorDependencyB", ":paramA")
            ->param(":paramA", "foo")
            ->method("setFoo")->args("foo");
            ->method("setBar")->args("bar");
            ->scope("singleton");

This translates into a config array internally and which can be retrieved by calling:

    $config = $builder->finalize();
    $yadif = new Yadif_Container($config);

The following methods are available on the builder:

- bind($componentName)
Registers a component with the name and automatically sets it as implementation also.

- to($implementationName)
Overwrite the implementation with this class

- args($arg1, $arg2, ...)
Depending on the context, set constructor or last registered method arguments. If you
have set a method, you can't set constructor arguments anymore.

- param($paramName, $paramValue)
Depending on the context, set constructor or last registered method parameter.

- method($methodName)
Register a method to be called when instantiating the implementation.

- scope($scope)
Set the scope of the component to "singleton" or "prototype"

- toProvider($callback)
Set a factory PHP callback variable, which is used instead of a new operation via reflection
to create the object.

13. Modules
=============

Configuration of single instances and interfaces can be quite cumbersome in large applications.
If you want to switch module implementations with each other it might be nice to be able
to group configurations. This is possible by extending the "Yadif_Module".

    class MyModule extends Yadif_Module
    {
        protected function configure()
        {
            $this->bind("Foo")->to("Bar")->args("foo", "bar");
            $this->bind("Bar")->to("Baz");
        }
    }

Internally the Module uses a Builder instance to create the config. To stuff this module into
Yadif, you have to transform it to a config and instantiate the Container:

    $module = new MyModule();
    $yadif = new Yadif_Container($module->getConfig());

Additionally the Module allows for a nice separation of different instance types, which
might help to structure your dependency injection needs:

    class MyNewModule extends Yadif_Module
    {
        protected function configure()
        {
            $this->bindHelpers();
            $this->bindDataAccessObjects();
            $this->bindServices();
            $this->bindDomainObjects();
        }

        private function bindHelpers()
        {
            $this->bind("Foo")->to("Bar");
        }

        private function bindDataAccessObjects()
        {
            // bindings
        }

        private function bindServices()
        {
            // bindings
        }

        private function bindDomainObjects()
        {
            // bindings
        }
    }

14. Clearing Instances
=============

Sometimes services have lots of internal state which is expensive memory wise, especially for long
running scripts like cron jobs. You can clear instances by calling:

    $container->clear('ComponentName');

15. TODOs
=============

- Add Dependency Management object, which allows to lazy load modules and their dependend
   on modules.
- Add Contextual Bindings via "Annotations" or "Names".