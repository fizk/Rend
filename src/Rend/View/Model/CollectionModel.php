<?php
namespace Rend\View\Model;

class CollectionModel extends ItemModel
{
    /**
     * Constructor
     *
     * @param  null|array|\Traversable $variables
     * @param  array|\Traversable $options
     */
    public function __construct(array $variables = null, $options = null)
    {
        $this->variables = $variables;
        if (is_array($options) || $options instanceof \Traversable) {
            foreach ($options as $key => $value) {
                $this->setOption($key, $value);
            }
        }
    }

    /**
     * Set HTTP range header.
     *
     * @param int $lower
     * @param int $upper
     * @param int $size Complete size of collection
     * @return $this
     */
    public function setRange(int $lower, int $upper, int $size)
    {
        $this->setOption('Access-Control-Expose-Headers', 'Range-Unit, Content-Range');
        $this->setOption('Range-Unit', "items");
        $this->setOption('Content-Range', "items {$lower}-{$upper}/{$size}");
        return $this;
    }
}
