<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 26/04/15
 * Time: 9:22 PM
 */

namespace Rend\View\Model;

use Traversable;

class ItemModel extends EmptyModel
{
    /**
     * Constructor
     *
     * @param  null|mixed $variables
     * @param  array|\Traversable $options
     */
    public function __construct($variables = null, $options = null)
    {
        $this->variables = $variables;
        if (is_array($options) || $options instanceof \Traversable) {
            foreach ($options as $key => $value) {
                $this->setOption($key, $value);
            }
        }
    }
}
