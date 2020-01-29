<?php


namespace App\Interfaces;


interface StructurePatternInterface
{
    public function getPattern($key = false): array;

    public function getTableName(): string;

    public function getFactoryName();
}