<?php
namespace Rend\View\Model;

use Zend\Form\Form;

/**
 * Class ErrorModel
 * @package Restvisi\View\Model
 */
class ErrorModel extends ItemModel
{
    /**
     * @var int
     */
    protected $statusCode = 500;

    /**
     * Constructor
     *
     * @param  \Zend\Form\Form|\Exception|string $variables
     * @param  array|\Traversable $options
     */
    public function __construct($variables = null, $options = null)
    {
        if ($variables instanceof Form) {
            $this->variables = $this->extractForm($variables);
        } elseif ($variables instanceof \Exception) {
            $this->variables = $this->extractException($variables);
        } elseif ($variables instanceof \Throwable) {
            $this->variables = $this->extractException($variables);
        } elseif (is_string($variables)) {
            $this->variables = $this->extractString($variables);
        } else {
            $this->variables = [];
        }

        if (is_array($options) || $options instanceof \Traversable) {
            foreach ($options as $key => $value) {
                $this->setOption($key, $value);
            }
        }
    }

    /**
     * Extract error messages from Form.
     *
     * @param Form $form
     * @return array
     */
    private function extractForm(Form $form): array
    {
        return array_map(function ($value, $key) {
            return [
                'field' => $key,
                'message' => array_values($value),
            ];
        }, $form->getMessages(), array_keys($form->getMessages()));

    }

    /**
     * Extract error messages from Exception.
     *
     * @param \Throwable $exception
     * @return array
     */
    private function extractException(\Throwable $exception): array
    {
        $messages = [];
        while ($exception) {
            $messages[] = [
                'message' => $exception->getMessage()
            ];
            $exception = $exception->getPrevious();
        }
        return $messages;
    }

    /**
     * Pack a string into error message array.
     *
     * @param string $string
     * @return array
     */
    private function extractString($string): array
    {
        return [
            ['message' => $string]
        ];
    }
}
