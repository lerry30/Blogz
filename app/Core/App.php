<?php
namespace App\Core;

/**
 * Main Application Class
 * Handles routing and dispatching of requests
 */
class App {
  protected $controller = 'HomeController';
  protected $method = 'index';
  protected $params = [];
  protected $routes = [];
  protected $middleware = [];
    
  /**
   * Constructor - Initialize the application
   */
  public function __construct() {
    // Load routes
    $this->loadRoutes();
        
    // Parse the URL
    $url = $this->parseUrl();
        
    // Route the request
    $this->routeRequest($url);
  }
    
  /**
   * Load routes from the routes file
   */
  private function loadRoutes() {
    $routesFile = __DIR__ . '/../../routes/web.php';
    if(file_exists($routesFile)) {
      require_once $routesFile;
            
      // Get routes defined in the routes file
      if(isset($routes) && is_array($routes)) {
        $this->routes = $routes;
      }
    }
  }
    
  /**
   * Parse the URL into controller, method and parameters
   */
  private function parseUrl() {
    if (isset($_GET['url'])) {
      // Trim trailing slash, sanitize URL, and explode by slash
      return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
    }
    return [];
  }
    
  /**
   * Route the request to the appropriate controller and method
   */
  private function routeRequest($url) {
    // Get the request URI and method
    $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
    $uri = strtok($uri, '?');  // Remove query string
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    // Check for direct route match
    if(isset($this->routes[$requestMethod][$uri])) {
      $this->handleRouteMatch($this->routes[$requestMethod][$uri]);
        return;
      }
      // Check for pattern routes
      foreach($this->routes[$requestMethod] ?? [] as $route => $handler) {
        // Convert route pattern to regex
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route);
        $pattern = "#^" . $pattern . "$#";
        if(preg_match($pattern, $uri, $matches)) {
//          var_dump($matches);die;
          // Remove numeric keys from matches
          foreach($matches as $key => $match) {
            if(is_int($key)) {
              unset($matches[$key]);
            }
          }
          $this->handleRouteMatch($handler, $matches);
          return;
        }
      }
      // If no routes match, use the URL segments for traditional routing
      if(!empty($url[0])) {
        $controllerName = ucfirst($url[0]) . 'Controller';
        $controllerFile = __DIR__ . '/../Controllers/' . $controllerName . '.php';
        if(file_exists($controllerFile)) {
          $this->controller = $controllerName;
          unset($url[0]);
          // Include the controller
          require_once $controllerFile;
          $controllerClass = "\\App\\Controllers\\{$this->controller}";
          $controller = new $controllerClass();
                // Check if method exists
                if (isset($url[1]) && method_exists($controller, $url[1])) {
                    $this->method = $url[1];
                    unset($url[1]);
                }
                
                // Get parameters
                $this->params = $url ? array_values($url) : [];
                
                // Call the controller method
                call_user_func_array([$controller, $this->method], $this->params);
                return;
            }
        }
        
        // If we reach here, no route was matched
        $this->handleNotFound();
    }
    
    /**
     * Handle a matched route
     */
  private function handleRouteMatch($handler, $params = []) {
    if (is_array($handler) && isset($handler['uses'])) {
      if (isset($handler['middleware'])) {
        $middlewareName = $handler['middleware'];

        if (isset($this->middleware[$middlewareName])) {
          $result = $this->applyMiddleware($middlewareName, $_REQUEST);
          if ($result === false) {
            return;
          }
        }
      }

      $handler = $handler['uses'];
    }

        if(is_callable($handler)) {
            // If the handler is a closure, call it directly
            call_user_func_array($handler, $params);
        } elseif (is_string($handler)) {
            // If the handler is a string like "HomeController@index"
            list($controller, $method) = explode('@', $handler);
            
            $controllerFile = __DIR__ . '/../Controllers/' . $controller . '.php';
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                $controllerClass = "\\App\\Controllers\\{$controller}";
                $controllerObj = new $controllerClass();
                
                if (method_exists($controllerObj, $method)) {
                    call_user_func_array([$controllerObj, $method], $params);
                } else {
                    $this->handleNotFound();
                }
            } else {
                $this->handleNotFound();
            }
        } else {
            $this->handleNotFound();
        }
    }
    
    /**
     * Handle 404 Not Found
     */
    private function handleNotFound()
    {
        header("HTTP/1.0 404 Not Found");
        echo "404 - Page Not Found";
    }
    
    /**
     * Register middleware
     */
    public function registerMiddleware($name, $middleware)
    {
        $this->middleware[$name] = $middleware;
    }
    
    /**
     * Apply middleware
     */
    public function applyMiddleware($name, $request)
    {
        if (isset($this->middleware[$name])) {
            return $this->middleware[$name]->handle($request);
        }
        
        return $request;
    }
}
