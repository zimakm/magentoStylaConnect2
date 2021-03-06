<?php
namespace Styla\Connect2\Model\Styla\Api\Request\Type;

use Styla\Connect2\Api\Styla\RequestInterface as StylaRequestInterface;
use Zend\Http\Request as HttpRequest;

abstract class AbstractType implements StylaRequestInterface
{
    protected $_requestPath;
    protected $_requestType;
    protected $_params;

    protected $_connectionType = HttpRequest::METHOD_GET;

    protected $_requestTimeout;
    protected $_requestConnectTimeout;

    /**
     *
     * @var \Styla\Connect2\Model\Styla\Api
     */
    protected $stylaApi;

    /**
     *
     * @var \Styla\Connect2\Helper\Data
     */
    protected $stylaHelper;

    public function __construct(
        \Styla\Connect2\Model\Styla\Api $stylaApi,
        \Styla\Connect2\Helper\Data $stylaHelper
    )
    {
        $this->stylaHelper = $stylaHelper;
        $this->stylaApi     = $stylaApi;
    }

    /**
     * Initialize this request with data to pass on to the api service
     *
     * @param string $requestPath
     * @param mixed  $params
     *
     * @return $this
     */
    public function initialize($requestPath, $params = null)
    {
        $this->_requestPath = $requestPath;
        $this->_params      = $params;

        return $this;
    }

    /**
     * Get request params (used for POST-type requests)
     *
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->_params = $params;
    }

    /**
     *
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * Get the curl connection type - GET/POST
     *
     * @return string
     */
    public function getConnectionType()
    {
        return $this->_connectionType;
    }

    /**
     * Set the curl connection type - GET/POST
     *
     * @param  $type
     */
    public function setConnectionType($type)
    {
        $this->_connectionType = $type;
    }

    /**
     * Get the request path of this request
     *
     * @param bool $urlEncoded
     * @return string $requestPath
     */
    public function getRequestPath($urlEncoded = false)
    {
        $requestPath = $this->_requestPath;
        if ($urlEncoded) {
            $requestPath = urlencode($requestPath);
        }

        return $requestPath;
    }

    /**
     * Get the type name of this request
     *
     * @return string
     */
    public function getRequestType()
    {
        return $this->_requestType;
    }

    /**
     * Get the class type name for the response type for this request
     *
     * @return string
     */
    public function getResponseType()
    {
        return $this->_requestType;
    }

    /**
     *
     * @return \Styla\Connect2\Model\Styla\Api
     */
    public function getApi()
    {
        return $this->stylaApi;
    }

    /**
     *
     * @return \Styla\Connect2\Helper\Data
     */
    public function getConfigHelper()
    {
        return $this->stylaHelper;
    }

    /**
     * Get the connection timeout settings for this request.
     *
     * @return false|array
     */
    public function getConnectionTimeoutOptions()
    {
        $options = [];

        if (null !== $this->_requestConnectTimeout) {
            $options[CURLOPT_CONNECTTIMEOUT] = $this->_requestConnectTimeout;
        }
        if (null !== $this->_requestTimeout) {
            $options[CURLOPT_TIMEOUT] = $this->_requestTimeout;
        }

        return empty($options) ? false : $options;
    }
}