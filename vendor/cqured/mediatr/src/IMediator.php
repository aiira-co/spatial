<?php

namespace Cqured\MediatR;

/**
 * Defines a mediator to encapsulate request/response and publishing interaction patterns
*/
interface IMediator
{
    
    /**
     * Asynchronously send a request to a single handler
    *
    * @param IRequest $request
    * @param CancellationToken $cancellationToken
    * @return void A task that represents the send operation. The task result contains the handler response
    */
    public function send(IRequest $request);


    
    
    

    /**
     * Asynchronously send a notification to multiple handlers
    *
    * @param object $notification
    * @param CancellationToken $cancellationToken
    * @return void A task that represents the publish operation
    */
    public function publish(object $notification);


}
