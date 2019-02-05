<?php
// namespace Cqured\MediatR;

 
    //  Defines a handler for a notification
     
    //  <typeparam name="TNotification">The type of notification being handled</typeparam>
    //  interface INotificationHandler
    //     where TNotification : INotification
    // {
         
    //      Handles a notification
         
    //      <param name="notification">The notification</param>
    //      <param name="cancellationToken">Cancellation token</param>
    //     Task Handle(TNotification notification, CancellationToken cancellationToken);
    // }

     
    //  Wrapper class for a synchronous notification handler
     
    //  <typeparam name="TNotification">The notification type</typeparam>
    // public abstract class NotificationHandler<TNotification> : INotificationHandler<TNotification>
    //     where TNotification : INotification
    // {
    //     Task INotificationHandler<TNotification>.Handle(TNotification notification, CancellationToken cancellationToken)
    //     {
    //         Handle(notification);
    //         return Unit.Task;
    //     }

         
    //      Override in a derived class for the handler logic
         
    //      <param name="notification">Notification</param>
    //     protected abstract void Handle(TNotification notification);
    // }