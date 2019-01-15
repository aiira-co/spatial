<?php
namespace Cqured\MediatR; 

/**
 * Factory method used to resolve all services. For multiple instances, it will resolve against <see cref="IEnumerable{T}" />
 */
class ServiceFactory
{

};

class ServiceFactoryExtensions
{
    public static function getInstance(ServiceFactory $factory)
    {
        return $factory;
    }

    public static function getInstances(ServiceFactory $factory)
    {
        return $factory;

    }
}
