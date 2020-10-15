<?php
namespace Rend\View\Strategy;

use Rend\View\Model\ModelInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\View\Renderer\RendererInterface;
use Laminas\View\ViewEvent;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\EventManager\ListenerAggregateTrait;

/**
 * Class MessageStrategy
 * @package Restvisi\View\Strategy
 */
class MessageStrategy implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * @var RendererInterface
     */
    protected $renderer;

    /**
     * @param RendererInterface $renderer
     */
    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     * @param int $priority
     * @return void
     */
    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach('renderer', [$this, 'selectRenderer'], 1000);
        $this->listeners[] = $events->attach('response', [$this, 'injectResponse'], 1000);
    }

    /**
     * @param ViewEvent $e
     * @return RendererInterface
     */
    public function selectRenderer(ViewEvent $e): ?RendererInterface
    {
        if (!$e->getModel() instanceof ModelInterface) {
            return null;
        }
        return $this->renderer;
    }

    /**
     * @param ViewEvent $e
     */
    public function injectResponse(ViewEvent $e): void
    {
        if (!$e->getModel() instanceof ModelInterface) {
            return;
        }

        $result   = $e->getResult();

        /** @var $model \Laminas\View\Model\ModelInterface */
        $model = $e->getModel();

        /** @var $response \Laminas\Http\PhpEnvironment\Response */
        $response = $e->getResponse();
        $response->setContent($result);

        if (get_class($model) == 'Rend\View\Model\EmptyModel') {//FIXME
            $response->setContent('');
        }
        $response->setStatusCode($model->getStatus());
        $headers = $response->getHeaders();
        foreach ($model->getOptions() as $key => $value) {
            $headers->addHeaderLine($key, $value);
        }
        $headers->addHeaderLine('Content-type', 'application/json; charset=utf-8');
        $headers->addHeaderLine('Access-Control-Allow-Origin', '*');
        if ($model->getStatus() == 206) {
            $headers->addHeaderLine('Access-Control-Expose-Headers', 'Range-Unit, Content-Range');
        }
    }
}
