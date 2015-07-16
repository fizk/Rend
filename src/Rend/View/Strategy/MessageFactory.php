<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 26/04/15
 * Time: 4:40 PM
 */

namespace Rend\View\Strategy;

use Rend\View\Renderer\MessageRenderer;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class MessageFactory
 * @package Restvisi\View\Strategy
 */
class MessageFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new MessageStrategy(new MessageRenderer());
    }
}
