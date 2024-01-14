<?php 
namespace Wpint\Route\Enums;

enum RouteHttpMethodEnum : string
{

    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case PATCH = 'PATCH';
    case DELETE = 'DELETE';
    case ANY = 'ANY';

}