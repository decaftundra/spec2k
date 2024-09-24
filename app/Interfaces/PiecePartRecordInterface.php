<?php

namespace App\Interfaces;

use App\Interfaces\WorkedPiecePartInterface;
use App\Interfaces\NextHigherAssemblyInterface;
use App\Interfaces\ReplacedPiecePartInterface;

interface PiecePartRecordInterface extends WorkedPiecePartInterface,
                                           ReplacedPiecePartInterface,
                                           NextHigherAssemblyInterface
{
    //
}