<?php
namespace Cqured\MediatR;

// use Psr\Http\Message\RequestInterface;
// use Psr\Http\Message\ResponseInterface;
// use Psr\Http\Message\ServerRequestInterface;

class Mediator implements IMediator
{

    /**
     * Pass in the model and it will call the handler
     * Based on the model name
     *
     * @param IRequest $request
     * @param array $params
     * @return ResponseInterface
     */
    public function send(IRequest $request)
    {
        if ($request == null) {
            throw new ArgumentNullException(get_class($request));
        }

        $handler = $this->getHandlerName($request);

        $handlerClass = new $handler;

        // if (count($params) > 0) {

        //     return new $this->handler->handle($this->setModelParams($request));
        // }

        return $handlerClass->handle($request);

    }

    public function publish(object $notification, array $params = [])
    {
        if ($notification == null) {
            throw new ArgumentNullException(nameof(notification));
        }
        // if ($notification instanceof('INotification'))
        // {
        //     return publishNotification($notification, $cancellationToken);
        // }

        // throw new ArgumentException($"{nameof(notification)} does not implement ${nameof(INotification)}");
    }

    /**
     * Override in a derived class to control how the tasks are awaited.
     * By default the implementation is a foreach and await of each handler
     *
     * @param IEnumerable $allHandlers
     * @return void
     */
    protected function asyncPublishCore(IEnumerable $allHandlers)
    {
        foreach ($handler as $allHandlers) {
            handler()->configureAwait(false);
        }
    }

    /**
     * pushNoti
     *
     * @param INotification $notification
     * @return void
     */
    private function publishNotification(INotification $notification)
    {
        $notificationType = $notification->getType();
        // $handler = $this->_notificationHandlers->getOrAdd($notificationType,
        //     t => (NotificationHandlerWrapper)Activator.CreateInstance(typeof(NotificationHandlerWrapperImpl<>).MakeGenericType(notificationType)));

        return $handler->handle($notification, $cancellationToken, $this->_serviceFactory, $publishCore);
    }

    /**
     * Set $request Properties value with Params
     *
     * @param ServerRequestInterface $request
     * @param array $params
     * @return ServerRequestInterface
     */
    private function setModelParams(IRequest $request, array $params): IRequest
    {
        // Get Array Keys as properties
        $properties = array_keys($params);

        // Set Propeties value of the $request model
        for ($i = 0; $i < count($params); $i++) {
            if (property_exists($request, $properties[$i])) {
                $request->$properties[$i] = $params[$properties[$i]];
            }
        }

        return $request;
    }

    /**
     * Generate Hanlder name from request name
     *
     * @param ServerRequestInterface $request
     * @return void
     */
    private function getHandlerName(IRequest $request): string
    {
        $requestNamespace = explode('\\', get_class($request));
        if (count($requestNamespace) > 1) {
            $modelName = $requestNamespace[count($requestNamespace) - 1];
        } else {
            $modelName = $requestNamespace;
        }
        $handlerName = $modelName . 'Handler';
        return str_replace($modelName, $handlerName, get_class($request));
    }
}
