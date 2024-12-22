<?php
describe('sum',function(){

    it('may sum integers',function(){
        $result = sum(3,4);
    
        expect($result)
        ->toBeInt()
        ->toBe(7);
    
    });


    it('may sum floats',function(){
        $result = sum(1.5,2.5);
    
        expect($result)
        ->toBeFloat()
        ->toBe(4.0);
    
    });
});

function sum(float|int $a, float|int $b):float|int{
    return $a + $b;
}