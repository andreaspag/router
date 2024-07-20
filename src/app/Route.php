<?php

namespace App;

class Route 
{
    /** @var string The user defined route */
    private $_userRoute = '';
    /** @var string The route method */
    private $_method = '';
    /** @var array The route data */
    private $_routeParams = [];
    /** @var string The matched path */
    private $_matched = '';
    /** @var array The param constraints */
    private $_routeParamConstraints = [];

    public function __construct($userRoute, $method) {
        $this->_userRoute = strtolower($userRoute);
        $this->_method = strtolower($method);
    }

    /**
     * Try to match the request to the defined route
     * @param string $request The request to match against
     * @param string $requestMethod The request method
     */
    public function matches($request, $requestMethod) {

        if(strtolower($requestMethod) !== $this->_method) {
            return false;
        }

        $routeVars = $this->_getRouteVars();
               
        $route_parts = $this->_getRouteParts();

        $route_parts_regEx = $this->_getRouteRegExParts($routeVars);
        
        // Get the regular expression that will be used to match the request
        $route_regEx = $this->_getRouteRegEx($route_parts, $route_parts_regEx);
        if( $this->_userRoute === $request || 
                (trim($request,'/') === trim($this->_userRoute,'/')) || 
                ((count($route_parts_regEx) > 0) && $route_regEx && preg_match($route_regEx, $request)) 
              ) {
                    // this route matched
                    $this->_routeParams = $this->_getRouteData($request, $route_regEx, $routeVars);
                    $this->_matched = $request;
                    return true;
            }
        // this route did not match
        return false;
    }

    /**
     * @return string The user defined route
     */
    public function getUserRoute() {
        return $this->_userRoute;
    }

    /**
     * Return the data of the route which matched the request
     */
    public function getRouteParams() {
        return $this->_routeParams;
    }
    
    /**
     * Return the matched path
     */
    public function getMatchedPath() {
        return $this->_matched;
    }

    /**
     *
     * @param string $route The user defined route
     * @return array An array of the route vars. Route vars are defined by the user in {placeholders}
     */
    private function _getRouteVars() {

        $route = $this->_userRoute;
        
        $regex = '/{(\w+)}/iu';
        
        $res = preg_match_all($regex, $route, $matches);

        if(count($matches[0])) {
            return $matches[0];
        }
        return array();
    }

     /**
     * @return array Return an array with the URI segments
     */
    private function _getRouteParts() {
      
        $route = $this->_userRoute;
        if(empty($route)) return '';
        return (explode('/', trim($route,'/')));
    }

    /**
     * Get the regular expression for the route vars. 
     * For each route var defined by user get the expression that will be used to get the value of a route var.
     * Route vars are defined by the user in {placeholders}
     *
     * @param array $routeParamsArray
     * @return array
     */
    private function _getRouteRegExParts($routeParamsArray) {

        if(count($routeParamsArray) == 0) return array();

        $regExParts = array();

        foreach($routeParamsArray as $param) {
            $paramName = preg_replace('/{|}/','',$param);
            $paramPattern = '([-_&@#$^\*\w\s]*)'; // the default param pattern
            // if there is a defined constraint for this param use that instead of the default
            if ($paramConstraint = $this->getParamConstraint($paramName)) {
                $paramPattern = $paramConstraint;
            }
            $regExParts[$paramName] = '(?P<'.$paramName.'>'.$paramPattern.')\/?';
        }
        return $regExParts;
    }

    /**
     * Build the regular expression to match the user defined route.
     * @param array $routeParts
     * @param array $regExParts
     * @return string
     */
    private function _getRouteRegEx($routeParts, $regExParts) {

        if(count($routeParts) == 0 || count($regExParts) == 0) 
            return '';

        $regEx = '';
        foreach($routeParts as $param) {

            $regEx.= '\/';
            $key       = preg_replace('/{|}/','',$param);
            $paramName = $key;
            if(array_key_exists($paramName, $regExParts)) {
                $regEx.= $regExParts[$paramName];
            } else {
                $regEx.= $param;
            }
        }
        $regEx.='\/?';

        return '/^'.$regEx.'$/ui';
    }

    /**
     * @param string $request
     * @param string $regEx
     * @param array $routeVars
     * @return array The values of the defined route vars. Route vars are defined by the user in {placeholders}
     */
    private function _getRouteData($request, $regEx, $routeVars) {

        if(count($routeVars) == 0) 
            return array();

        $res = preg_match_all($regEx, $request, $matches);
        $res = array();
  
        if(count($matches)) {
            
            foreach($routeVars as $index=>$param) {
                $paramName = preg_replace('/{|}/','',$param);

                if( isset($matches[$paramName]) && isset($matches[$paramName][0]) ) {
                    $res[$paramName] = $matches[$paramName][0];
                }
            }//~foreach
        }//~if
        return $res;
    }

    /**
     * Allow the definition of a regular expression/constraint
     * which will be used to validate the route param value
     * @param $param
     * @param $regEx
     * @return $this
     */
    public function where($param, $regEx) {
       $this->_routeParamConstraints[$param] = $regEx;
       return $this;
    }

    /**
     * Get the defined param constraint/regular expression
     * which will be used to validate the param value
     * @param $param
     * @return string
     */
    private function getParamConstraint($param) {
        return ($this->_routeParamConstraints[$param] ?? '');
    }

}//~CLASS