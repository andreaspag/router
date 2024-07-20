<?php

namespace App;

class Dispatcher
{
    
    private function __construct() {
    }

    private function __clone() {
    }

    /**
     *
     * @param array $dispatch
     * @return bool | array
     */
    public static function dispatch(array $dispatch) {

        $handler = $dispatch['handler'] ?? null;
        $route = $dispatch['route'] ?? null;
        $request = $dispatch['request'] ?? null;

        if(is_string($handler)) {

            // Check if a method exists
            if(function_exists($handler)) {
                if(count($route->getRouteParams()) == 0) {
                    $handler();
                } else {
                    call_user_func_array($handler, $route->getRouteParams());
                }
                return array('path'=>$route->getMatchedPath(),'handler'=>$handler);
            } else {
                //"method $handler does not exist;
            }
            
            // Check for a class@method string
            if(strpos($handler, '@')!==0) {
                list($class, $method) = explode('@', $handler);
                if(class_exists($class)) {
                    $handlerClass = new $class;
                    if(method_exists($handlerClass,$method)) {
                        if(count($route->getRouteParams()) == 0) {
                            $handlerClass->$method();
                        }
                        else {
                            call_user_func_array(array($handlerClass,$method),$route->getRouteParams());
                        }
                        return array('path'=>$route->getMatchedPath(),'handler'=>$handler);
                   }
                }
            }

            // Check for callable 
            if(class_exists($handler)) { 
                
                $obj = new $handler();
                // Call if callable
                if(is_callable($obj)) {
                    $obj();
                    return array('path'=>$route->getMatchedPath(), 'handler'=>$handler);
                }
            }

        }//~if(is_string)
        else if(is_callable($handler) && ($handler instanceof \Closure)) {

            if(count($route->getRouteParams()) == 0) {
                $handler();
            } else {
                call_user_func_array($handler, $route->getRouteParams());
            }
            return array('path'=>$route->getMatchedPath(),'handler'=>'closure');
        }
        return false;
    }   

}