<?php
namespace Rend\View\Model;

/**
 * Interface ModelInterface
 * @package Restvisi\View\Model
 */
interface ModelInterface
{
    /**
     * Set HTTP location header
     * @param $location
     * @return ModelInterface
     */
    public function setLocation(string $location);

    /**
     * Set HTTP length header.
     *
     * @param int $length
     * @return ModelInterface
     */
    public function setLength(int $length);

    /**
     * Set HTTP status code.
     *
     * @param $code
     * @return ModelInterface
     */
    public function setStatus(int $code);

    /**
     * Get HTTP status code.
     *
     * @return int
     */
    public function getStatus(): int;

    /**
     * @return string
     */
    public function toJson(): string;
}
