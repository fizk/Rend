<?php
namespace Rend\Helper\Http;

class RangeValue
{
    /** @var  int */
    private $from;

    /** @var  int */
    private $to;

    /**
     * @return int
     */
    public function getFrom(): ?int
    {
        return $this->from;
    }

    /**
     * @param int $from
     * @return RangeValue
     */
    public function setFrom(?int $from): RangeValue
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @return int
     */
    public function getTo(): ?int
    {
        return $this->to;
    }

    /**
     * @param int $to
     * @return RangeValue
     */
    public function setTo(?int $to): RangeValue
    {
        $this->to = $to;
        return $this;
    }

    public function getSize(): ?int
    {
        if ($this->to === null) {
            return null;
        } else {
            return ($this->to - $this->from) >= 0 ? ($this->to - $this->from) : 0;
        }
    }

    public function __toString()
    {
        return $this->to
            ? "{$this->from}-{$this->to}"
            : "{$this->from}-";
    }

}
