<?php
namespace Cqured\MediatR;

/// <summary>
/// Marker interface to represent a request with a void response
/// </summary>
interface IRequest
{}

/// <summary>
/// Marker interface to represent a request with a response
/// </summary>
/// <typeparam name="TResponse">Response type</typeparam>
interface IRequestAsync extends IBaseRequest
{}

/// <summary>
/// Allows for generic type constraints of objects implementing IRequest or IRequest{TResponse}
/// </summary>
interface IBaseRequest
{}
