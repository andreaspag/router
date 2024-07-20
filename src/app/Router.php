<?php

namespace App;

class Router
{
    public const NOT_FOUND = 0;
    public const FOUND = 1;
    /**
     *
     * @var array Holds the user routes
     */
    private array $_routes = array(
        'get' => [],
        'post' => [],
        'put' => [],
        'delete' => []
    );

    private ?string $_prefix = null;
    private ?Route $_matchedRoute = null;
   
    public function __construct() {
        
    }

    private function addGet($route, $handler) {
        $route = $this->parseUserRoute($route);
        $r = new Route($route, 'GET');
        $this->_routes['get'][$route] = array('route'=> $r, 'handler'=>$handler,'method'=>'GET');
        return $r;
    }

    private function addPost($route, $handler) {
        $route = $this->parseUserRoute($route);
        $r = new Route($route, 'POST');
        $this->_routes['post'][$route] = array('route'=> $r, 'handler'=>$handler,'method'=>'POST');
        return $r;
    }

    private function addPut($route, $handler) {
        $route = $this->parseUserRoute($route);
        $r = new Route($route, 'PUT');
        $this->_routes['put'][$route] = array('route'=> $r, 'handler'=>$handler, 'method'=>'PUT');
        return $r;
    }

    private function addDelete($route, $handler) {
        $route = $this->parseUserRoute($route);
        $r = new Route($route, 'DELETE');
        $this->_routes['delete'][$route] = array('route'=> $r, 'handler'=>$handler, 'method'=>'DELETE');
        return $r;
    }

    /**
     * Adds a route for a GET request
     * @param string $route
     * @param callable | string The handler to be executed if the route matches the request
     */
    public function get($route, $handler) {
        return $this->addGet($route, $handler);
    }

    /**
     * Adds a route for a PUT request
     * @param string $route
     * @param callable | string The handler to be executed if the route matches the request
     */    
    public function put($route, $handler) {
        $this->addPut($route, $handler);
    }

    /**
     * Adds a route for a DELETE request
     * @param string $route
     * @param callable | string The handler to be executed if the route matches the request
     */
    public function delete($route, $handler) {
        $this->addDelete($route, $handler);
    }

    /**
     * Adds a route for a POST request
     * @param string $route
     * @param callable | string The handler to be executed if the route matches the request
     */
    public function post($route, $handler) {
        $this->addPost($route, $handler);
    }

    /**
     * @return array|int
     */
    public function match() {

        $request = $this->_getRequestStr();
        $requestMethod = $this->_getRequestMethod();

        foreach($this->_routes[$requestMethod] as $r=>$routeConfig) {

            // Try to match this route against the request
            $route   = $routeConfig['route'];
            $handler = $routeConfig['handler'];

            if($route->matches($request, $requestMethod)) {

                $this->_matchedRoute = $route;

                return [
                    'request' => $request,
                    'route' => $route,
                    'handler' => $handler
                ];               
            }
        }
        return self::NOT_FOUND;
    }

    /**
     * @return string
     */
    private function _getRequestMethod() { 
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @return string
     */
    private function _getRequestStr() {

        $requestStr = $_SERVER['REQUEST_URI'];
        $qString    = strpos($requestStr,'?');
        if($qString) {
            return substr($requestStr,0, $qString);
        }
        return $requestStr;
    }

    public function prefix($prefix, $callback) {
        $this->_prefix = $prefix;
        $callback();
        $this->_prefix = null;
    }

    public function hasPrefix() {
        return !empty($this->_prefix);
    }

    public function addPrefix($route) {
        if (!$this->hasPrefix()) {
            return $route;
        }
        return rtrim($this->_prefix,'/'). '/' . ltrim($route,'/');
    }

    private function parseUserRoute($route) {
        if ($this->hasPrefix()) {
            $route = $this->addPrefix($route);
        }
        return $route;
    }

    public function getMatchedRoute(): Route {
        return $this->_matchedRoute;
    }

}//~CLASS
