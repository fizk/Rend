<?php
namespace Rend\View\Model;

use \Zend\View\Model\ModelInterface as BaseModelInterface;

class EmptyModel implements ModelInterface, BaseModelInterface
{
    protected $terminate = true;

    protected $variables;

    protected $options = [];

    protected $statusCode = 200;

    /**
     * Constructor
     *
     * @param  array|\Traversable $options
     */
    public function __construct($options = null)
    {
        if (is_array($options) || $options instanceof \Traversable) {
            foreach ($options as $key => $value) {
                $this->setOption($key, $value);
            }
        }
    }

    /**
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->variables);
    }

    /**
     * Retrieve an external iterator
     * @return array
     */
    public function getIterator()
    {
        return [];
    }

    /**
     * Set renderer option/hint
     *
     * @param  string $name
     * @param  mixed $value
     * @return ModelInterface
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
        return $this;
    }

    /**
     * Set renderer options/hints en masse
     *
     * @param  array|\Traversable $options
     * @return ModelInterface
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Get renderer options/hints
     *
     * @return array|\Traversable
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set HTTP location header.
     *
     * @param string $location
     * @return $this
     */
    public function setLocation(string $location)
    {
        $this->setOption('Location', $location);
        return $this;
    }

    /**
     * Set HTTP length header.
     *
     * @param int $length
     * @return $this
     */
    public function setLength(int $length)
    {
        $this->setOption('Content-Length', $length);
        return $this;
    }

    /**
     * @param array $allow
     * @return $this
     */
    public function setAllow(array $allow)
    {
        $this->setOption('Allow', implode(',', $allow));
        return $this;
    }

    /**
     * Set HTTP status code.
     *
     * @param $code
     * @return $this
     */
    public function setStatus(int $code)
    {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * Get HTTP status code.
     *
     * @return int
     */
    public function getStatus(): int
    {
        return $this->statusCode;
    }

    /**
     * Get a single view variable
     *
     * @param  string $name
     * @param  mixed|null $default (optional) default value if the variable is not present.
     * @return mixed
     */
    public function getVariable($name, $default = null)
    {
        return null;
    }

    /**
     * Set view variable
     *
     * @param  string $name
     * @param  mixed $value
     * @return ModelInterface
     */
    public function setVariable($name, $value)
    {
        return $this;
    }

    /**
     * Set view variables en masse
     *
     * @param  array|\ArrayAccess $variables
     * @return ModelInterface
     */
    public function setVariables($variables)
    {
        return $this;
    }

    /**
     * Get view variables
     *
     * @return array|\ArrayAccess
     */
    public function getVariables()
    {
        return [];
    }

    /**
     * Set the template to be used by this model
     *
     * @param  string $template
     * @return ModelInterface
     */
    public function setTemplate($template)
    {
        return $this;
    }

    /**
     * Get the template to be used by this model
     *
     * @return string
     */
    public function getTemplate()
    {
        return '';
    }

    /**
     * Add a child model
     *
     * @param  BaseModelInterface $child
     * @param  null|string $captureTo Optional; if specified, the "capture to" value to set on the child
     * @param  null|bool $append Optional; if specified, append to child  with the same capture
     * @return ModelInterface
     */
    public function addChild(BaseModelInterface $child, $captureTo = null, $append = false)
    {
        return $this;
    }

    /**
     * Return all children.
     *
     * Return specifies an array, but may be any iterable object.
     *
     * @return array
     */
    public function getChildren()
    {
        return [];
    }

    /**
     * Does the model have any children?
     *
     * @return bool
     */
    public function hasChildren()
    {
        return false;
    }

    /**
     * Set the name of the variable to capture this model to, if it is a child model
     *
     * @param  string $capture
     * @return ModelInterface
     */
    public function setCaptureTo($capture)
    {
        return $this;
    }

    /**
     * Get the name of the variable to which to capture this model
     *
     * @return string
     */
    public function captureTo()
    {
        return '';
    }

    /**
     * Set flag indicating whether or not this is considered a terminal or standalone model
     *
     * @param  bool $terminate
     * @return ModelInterface
     */
    public function setTerminal($terminate)
    {
        return $this;
    }

    /**
     * Is this considered a terminal or standalone model?
     *
     * @return bool
     */
    public function terminate()
    {
        return true;
    }

    /**
     * Set flag indicating whether or not append to child  with the same capture
     *
     * @param  bool $append
     * @return ModelInterface
     */
    public function setAppend($append)
    {
        return $this;
    }

    /**
     * Is this append to child  with the same capture?
     *
     * @return bool
     */
    public function isAppend()
    {
        return false;
    }

    /**
     * The return value is cast to an integer.
     */
    public function count(): int
    {
        return 0;
    }
}
