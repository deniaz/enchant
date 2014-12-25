<?php

namespace Enchant;

use Enchant\Event\AfterHandleEvent;
use Enchant\Event\BeforeRouteEvent;
use FastRoute\Dispatcher\GroupCountBased as Dispatcher;
use FastRoute\DataGenerator\GroupCountBased as Datagenerator;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use League\Event\Emitter;
use League\Plates\Engine;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Aura\Di\Container;
use Aura\Di\Factory;

/**
 * Class Application
 * @package Enchant
 */
class Application implements HttpKernelInterface
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var RouteCollector
     */
    private $router;

    /**
     * @var Emitter
     */
    private $emitter;

    /**
     *
     */
    public function __construct()
    {
        $this->container = new Container(new Factory);
        $this->router = new RouteCollector(new Std, new DataGenerator);
        $this->emitter = new Emitter();

        $this->container->set('template.engine', new Engine());
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        try {
            $this->emitter->emit(new BeforeRouteEvent);
            $dispatcher = new Dispatcher($this->router->getData());
            $action = $dispatcher->dispatch($request->getMethod(), $request->getPathInfo());

            $response = call_user_func($action[1]);
            $this->emitter->emit(new AfterHandleEvent);
            return $response;
        } catch(\Exception $e) {
            return new Response($e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     */
    public function run(Request $request = null)
    {
        if (null === $request) {
            $request = Request::createFromGlobals();
        }

        $response = $this->handle($request);
        $response->send();
    }

    /**
     * @param $route
     * @param callable $action
     */
    public function get($route, callable $action)
    {
        $this->router->addRoute('GET', $route, $action);
    }

    /**
     * @param $route
     * @param callable $action
     */
    public function put($route, callable $action)
    {
        $this->router->addRoute('PUT', $route, $action);
    }

    /**
     * @param $route
     * @param callable $action
     */
    public function post($route, callable $action)
    {
        $this->router->addRoute('POST', $route, $action);
    }

    /**
     * @param $route
     * @param callable $action
     */
    public function delete($route, callable $action)
    {
        $this->router->addRoute('DELETE', $route, $action);
    }

    public function listen($event, $listener)
    {
        $this->emitter->addListener($event, $listener);
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function setViewDirectory($directory)
    {
        $this->container->get('template.engine')->setDirectory($directory);
    }
}