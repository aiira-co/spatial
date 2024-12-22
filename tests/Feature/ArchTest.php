<?php


use Spatial\Core\Attributes\ApiController;
use Spatial\Core\Attributes\Area;
use Spatial\Core\Attributes\Route;

use Common\Libraries\Controller;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Spatial\Core\Attributes\Injectable;

describe('App Globals',function(){

    test('strict types',function($namespace){
        expect($namespace)
            ->toUseStrictTypes();
    })->with(['Presentation','Common','Infrastructure','Core\Domain']);


    
});


describe('Common', function () {
    arch('Enums')
        ->expect('Common\Enums')
            ->toBeEnum()
            ->toBeIntBackedEnum()
            ->toHaveSuffix('Enum');

    arch('Interface')
        ->expect('Common\Interfaces')
        ->toBeInterface()
        ->toHaveSuffix('Interface');

});


describe('Api Controllers', function () {

    arch('Controllers',function ($namespace){
        expect($namespace)
            ->classes()
            ->toHaveSuffix('Controller')
            ->toHaveAttribute(ApiController::class)
            ->toHaveAttribute(Area::class)
            ->toHaveAttribute(Route::class)
            ->toExtend(Controller::class)
            ->toBeInvokable();
    })->with([
        'Presentation\ArtistApi\Controllers',
        'Presentation\BlogApi\Controllers',
        'Presentation\ControlApi\Controllers',
        'Presentation\EventApi\Controllers',
        'Presentation\IdentityApi\Controllers',
        'Presentation\MessengerApi\Controllers',
        'Presentation\PlayApi\Controllers',
        'Presentation\SocialApi\Controllers',
        'Presentation\StoreApi\Controllers',
        'Presentation\SuiteApi\Controllers'
    ]);

});


describe('Domain Models', function () {
    arch('Entities',function ($namespace){
        expect($namespace)
            ->classes()
            ->toHaveAttribute(Entity::class)
            ->toHaveAttribute(Table::class);
    })->with([
        'Core\Domain\Identity',
        'Core\Domain\Ori',
        'Core\Domain\Suite',
        'Core\Domain\Payment',
        'Core\Domain\Archive',


    ]);
});



describe('Infrastructure', function () {

    arch('Resources')
    ->expect(['Infrastructure\Resource','Infrastructure\Identity'])
    ->classes()
    ->toHaveSuffix('DB')
    ->toHaveAttribute(Injectable::class)
     ->not->toBeUsedIn('Common');


    arch('Services')
    ->expect('Infrastructure\Services')
    ->classes()
    ->toHaveSuffix('Service')
    ->toHaveAttribute(Injectable::class);
    
});