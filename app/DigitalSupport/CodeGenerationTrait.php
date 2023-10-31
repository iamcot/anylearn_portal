<?php namespace App\DigitalSupport;

trait CodeGenerationTrait 
{
    private function generate($partnerKey) 
    {
        return "1234567$partnerKey";
    }
}

