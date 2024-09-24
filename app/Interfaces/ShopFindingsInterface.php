<?php

namespace App\Interfaces;

use App\Interfaces\ShippedLruInterface;
use App\Interfaces\RemovedLruInterface;
use App\Interfaces\ReceivedLruInterface;
use App\Interfaces\LinkingFieldsInterface;
use App\Interfaces\ApuInformationInterface;
use App\Interfaces\ShopActionDetailInterface;
use App\Interfaces\EngineInformationInterface;
use App\Interfaces\ShopProcessingTimeInterface;
use App\Interfaces\AccumulatedTimeTextInterface;
use App\Interfaces\AirframeInformationInterface;

interface ShopFindingsInterface extends ShippedLruInterface,
                                        RemovedLruInterface,
                                        ReceivedLruInterface,
                                        LinkingFieldsInterface,
                                        ApuInformationInterface,
                                        ShopActionDetailInterface,
                                        EngineInformationInterface,
                                        ShopProcessingTimeInterface,
                                        AccumulatedTimeTextInterface,
                                        AirframeInformationInterface
{
    //
}