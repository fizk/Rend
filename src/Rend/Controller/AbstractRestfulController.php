<?php
namespace Rend\Controller;

use Rend\Helper\Http\MultiPart;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\ModelInterface;
use Laminas\Mvc\Controller\AbstractController;
use Laminas\Mvc\MvcEvent;
use Laminas\Http\Request as HttpRequest;
use Laminas\Json\Json;
use Laminas\Mvc\Exception;
use Laminas\Stdlib\RequestInterface as Request;
use Laminas\Stdlib\ResponseInterface as Response;

abstract class AbstractRestfulController extends AbstractController
{
    const CONTENT_TYPE_JSON = 'json';

    /**
     * {@inheritDoc}
     */
    protected $eventIdentifier = __CLASS__;

    /**
     * @var array
     */
    protected $contentTypes = [
        self::CONTENT_TYPE_JSON => [
            'application/hal+json',
            'application/json'
        ]
    ];

    /**
     * Name of request or query parameter containing identifier
     *
     * @var string
     */
    protected $identifierName = 'id';

    /**
     * @var int From Laminas\Json\Json
     */
    protected $jsonDecodeType = Json::TYPE_ARRAY;

    /**
     * Map of custom HTTP methods and their handlers
     *
     * @var array
     */
    protected $customHttpMethodsMap = [];

    /**
     * Set the route match/query parameter name containing the identifier
     *
     * @param  string $name
     * @return self
     */
    public function setIdentifierName($name)
    {
        $this->identifierName = (string) $name;
        return $this;
    }

    /**
     * Retrieve the route match/query parameter name containing the identifier
     *
     * @return string
     */
    public function getIdentifierName()
    {
        return $this->identifierName;
    }

    /**
     * Create a new resource
     *
     * @param  mixed $data
     * @return ModelInterface
     */
    public function post($data)
    {
        return (new EmptyModel())
            ->setStatus(405);
    }

    /**
     * Create a new resources
     *
     * @param  mixed $id
     * @param  mixed $data
     * @return ModelInterface
     */
    public function postList($id, $data)
    {
        return (new EmptyModel())
            ->setStatus(405);
    }

    /**
     * Delete an existing resource
     *
     * @param  mixed $id
     * @return ModelInterface
     */
    public function delete($id)
    {
        return (new EmptyModel())
            ->setStatus(405);
    }

    /**
     * Delete the entire resource collection
     *
     * @return ModelInterface
     */
    public function deleteList($data)
    {
        return (new EmptyModel())
            ->setStatus(405);
    }

    /**
     * Return single resource
     *
     * @param  mixed $id
     * @return ModelInterface
     */
    public function get($id)
    {
        return (new EmptyModel())
            ->setStatus(405);
    }

    /**
     * Return list of resources
     *
     * @return ModelInterface
     */
    public function getList()
    {
        return (new EmptyModel())
            ->setStatus(405);
    }

    /**
     * Retrieve HEAD metadata for the resource
     *
     * @param  null|mixed $id
     * @return ModelInterface
     */
    public function head($id = null)
    {
        return (new EmptyModel())
            ->setStatus(405);
    }

    /**
     * Respond to the OPTIONS method
     *
     * Typically, set the Allow header with allowed HTTP methods, and
     * return the response.
     *
     * @return ModelInterface
     */
    public function options()
    {
        return (new EmptyModel())
            ->setStatus(405);
    }


    /**
     * Respond to the OPTIONS method
     *
     * Typically, set the Allow header with allowed HTTP methods, and
     * return the response.
     *
     * @return ModelInterface
     */
    public function optionsList()
    {
        return (new EmptyModel())
            ->setStatus(405);
    }

    /**
     * Respond to the PATCH method
     *
     * @param  $id
     * @param  $data
     * @return ModelInterface
     */
    public function patch($id, $data)
    {
        return (new EmptyModel())
            ->setStatus(405);
    }

    /**
     * Modify a resource collection without completely replacing it
     *
     * @param  mixed $data
     * @return ModelInterface
     */
    public function patchList($data)
    {
        return (new EmptyModel())
            ->setStatus(405);
    }

    /**
     * Replace an entire resource collection
     *
     * @param  mixed $data
     * @return ModelInterface
     */
    public function putList($data)
    {
        return (new EmptyModel())
            ->setStatus(405);
    }

    /**
     * Update an existing resource
     *
     * @param  mixed $id
     * @param  mixed $data
     * @return ModelInterface
     */
    public function put($id, $data)
    {
        return (new EmptyModel())
            ->setStatus(405);
    }

    /**
     * Basic functionality for when a page is not available
     *
     * @return ModelInterface
     */
    public function notFoundAction()
    {
        return (new ErrorModel('Resource Not Found'))
            ->setStatus(404);
    }

    /**
     * Dispatch a request
     *
     * If the route match includes an "action" key, then this acts basically like
     * a standard action controller. Otherwise, it introspects the HTTP method
     * to determine how to handle the request, and which method to delegate to.
     *
     * @events dispatch.pre, dispatch.post
     * @param  Request $request
     * @param  null|Response $response
     * @return mixed|Response
     * @throws Exception\InvalidArgumentException
     */
    public function dispatch(Request $request, Response $response = null)
    {
        if (! $request instanceof HttpRequest) {
            throw new Exception\InvalidArgumentException(
                'Expected an HTTP request'
            );
        }

        return parent::dispatch($request, $response);
    }

    /**
     * Handle the request
     *
     * @todo   try-catch in "patch" for patchList should be removed in the future
     * @param  MvcEvent $e
     * @return mixed
     * @throws Exception\DomainException if no route matches in event or invalid HTTP method
     */
    public function onDispatch(MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();
        if (! $routeMatch) {
            throw new Exception\DomainException(
                'Missing route matches; unsure how to retrieve action'
            );
        }

        $request = $e->getRequest();

        // Was an "action" requested?
        $action  = $routeMatch->getParam('action', false);
        if ($action) {
            // Handle arbitrary methods, ending in Action
            $method = static::getMethodFromAction($action);
            if (! method_exists($this, $method)) {
                $method = 'notFoundAction';
            }
            $return = $this->$method();
            $e->setResult($return);
            return $return;
        }

        // RESTful methods
        $method = $this->resolveMethod($request);
        switch ($method) {
            // Custom HTTP methods (or custom overrides for standard methods)
            case (isset($this->customHttpMethodsMap[$method])):
                $callable = $this->customHttpMethodsMap[$method];
                $action = $method;
                $return = call_user_func($callable, $e);
                break;
            // DELETE
            case 'delete':
                $id = $this->getIdentifier($routeMatch, $request);
                $data = $this->processBodyContent($request);

                if ($id !== false) {
                    $action = 'delete';
                    $return = $this->delete($id);
                    break;
                }

                $action = 'deleteList';
                $return = $this->deleteList($data);
                break;
            // GET
            case 'get':
                $id = $this->getIdentifier($routeMatch, $request);
                if ($id !== false) {
                    $action = 'get';
                    $return = $this->get($id);
                    break;
                }
                $action = 'getList';
                $return = $this->getList();
                break;
            // HEAD
            case 'head':
                $id = $this->getIdentifier($routeMatch, $request);
                if ($id === false) {
                    $id = null;
                }
                $action = 'head';
                $headResult = $this->head($id);
                $response = ($headResult instanceof Response) ? clone $headResult : $e->getResponse();
                $response->setContent('');
                $return = $response;
                break;
            // OPTIONS
            case 'options':
                $id = $this->getIdentifier($routeMatch, $request);
                if ($id !== false) {
                    $action = 'options';
                    $return = $this->options($id);
                    break;
                }
                $action = 'optionsList';
                $return = $this->optionsList();
                break;
            // PATCH
            case 'patch':
                $id = $this->getIdentifier($routeMatch, $request);
                $data = (new MultiPart())->parse($request);

                if ($id !== false) {
                    $action = 'patch';
                    $return = $this->patch($id, $data);
                    break;
                }

                $action = 'patchList';
                $return = $this->patchList($data);
                break;
            // POST
            case 'post':
                $id = $this->getIdentifier($routeMatch, $request);
                $data = $this->processPostData($request);

                if ($id == false) {
                    $action = 'post';
                    $return = $this->post($data);
                    break;
                }

                $action = 'postList';
                $return = $this->postList($id, $data);
                break;
            // PUT
            case 'put':
                $id   = $this->getIdentifier($routeMatch, $request);
                $data = (new MultiPart())->parse($request);

                if ($id !== false) {
                    $action = 'put';
                    $return = $this->put($id, $data);
                    break;
                }

                $action = 'putList';
                $return = $this->putList($data);
                break;
            // All others...
            default:
                $response = $e->getResponse();
                $response->setStatusCode(405);
                return $response;
        }

        $routeMatch->setParam('action', $action);
        $e->setResult($return);
        return $return;
    }

    /**
     * Process post data and call create
     *
     * @param Request $request
     * @return mixed
     */
    public function processPostData(Request $request)
    {
        if ($this->requestHasContentType($request, self::CONTENT_TYPE_JSON)) {
            return Json::decode($request->getContent(), $this->jsonDecodeType);
        } else {
            return $request->getPost()->toArray();
        }
    }

    /**
     * Check if request has certain content type
     *
     * @param  Request $request
     * @param  string|null $contentType
     * @return bool
     */
    public function requestHasContentType(Request $request, $contentType = '')
    {
        /** @var $headerContentType \Laminas\Http\Header\ContentType */
        $headerContentType = $request->getHeaders()->get('content-type');
        if (!$headerContentType) {
            return false;
        }

        $requestedContentType = $headerContentType->getFieldValue();
        if (strstr($requestedContentType, ';')) {
            $headerData = explode(';', $requestedContentType);
            $requestedContentType = array_shift($headerData);
        }
        $requestedContentType = trim($requestedContentType);
        if (array_key_exists($contentType, $this->contentTypes)) {
            foreach ($this->contentTypes[$contentType] as $contentTypeValue) {
                if (stripos($contentTypeValue, $requestedContentType) === 0) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Register a handler for a custom HTTP method
     *
     * This method allows you to handle arbitrary HTTP method types, mapping
     * them to callables. Typically, these will be methods of the controller
     * instance: e.g., array($this, 'foobar'). The typical place to register
     * these is in your constructor.
     *
     * Additionally, as this map is checked prior to testing the standard HTTP
     * methods, this is a way to override what methods will handle the standard
     * HTTP methods. However, if you do this, you will have to retrieve the
     * identifier and any request content manually.
     *
     * Callbacks will be passed the current MvcEvent instance.
     *
     * To retrieve the identifier, you can use "$id =
     * $this->getIdentifier($routeMatch, $request)",
     * passing the appropriate objects.
     *
     * To retrieve the body content data, use "$data = $this->processBodyContent($request)";
     * that method will return a string, array, or, in the case of JSON, an object.
     *
     * @param  string $method
     * @param  Callable $handler
     * @return AbstractRestfulController
     */
    public function addHttpMethodHandler($method, /* Callable */ $handler)
    {
        if (!is_callable($handler)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Invalid HTTP method handler: must be a callable; received "%s"',
                (is_object($handler) ? get_class($handler) : gettype($handler))
            ));
        }
        $method = strtolower($method);
        $this->customHttpMethodsMap[$method] = $handler;
        return $this;
    }

    /**
     * Retrieve the identifier, if any
     *
     * Attempts to see if an identifier was passed in either the URI or the
     * query string, returning it if found. Otherwise, returns a boolean false.
     *
     * @param  \Laminas\Router\RouteMatch $routeMatch
     * @param  Request $request
     * @return false|mixed
     */
    protected function getIdentifier($routeMatch, $request)
    {
        if ($identifierName = $routeMatch->getParam('identifier', false)) {
            $this->setIdentifierName($identifierName);
        }

        $identifier = $this->getIdentifierName();
        $id = $routeMatch->getParam($identifier, false);
        if ($id !== false) {
            return $id;
        }

        $id = $request->getQuery()->get($identifier, false);
        if ($id !== false) {
            return $id;
        }

        return false;
    }

    /**
     * Process the raw body content
     *
     * If the content-type indicates a JSON payload, the payload is immediately
     * decoded and the data returned. Otherwise, the data is passed to
     * parse_str(). If that function returns a single-member array with a key
     * of "0", the method assumes that we have non-urlencoded content and
     * returns the raw content; otherwise, the array created is returned.
     *
     * @param  mixed $request
     * @return object|string|array
     */
    protected function processBodyContent($request)
    {
        $content = $request->getContent();

        // JSON content? decode and return it.
        if ($this->requestHasContentType($request, self::CONTENT_TYPE_JSON)) {
            return Json::decode($content, $this->jsonDecodeType);
        }

        parse_str($content, $parsedParams);

        // If parse_str fails to decode, or we have a single element with key
        // 0, return the raw content.
        if (!is_array($parsedParams)
            || (1 == count($parsedParams) && isset($parsedParams[0]))
        ) {
            return $content;
        }

        return $parsedParams;
    }

    /**
     * HTTP-Override takes precedence over Method.
     *
     * @param \Laminas\Stdlib\RequestInterface $request
     * @return string
     */
    private function resolveMethod($request)
    {
        /** @var  $overwrite \Laminas\Http\Header\GenericHeader */
        if ($overwrite = $request->getHeader('X-Http-Method-Override', null)) {
            return strtolower($overwrite->getFieldValue());
        }
        return strtolower($request->getMethod());
    }
}
