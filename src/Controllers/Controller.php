<?php

namespace App\Controller;

use App\ControllerTrait;
use App\Interfaces\ControllerInterface;

abstract class Controller implements ControllerInterface
{
    use ControllerTrait;

}