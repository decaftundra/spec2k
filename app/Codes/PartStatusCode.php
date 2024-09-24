<?php

namespace App\Codes;

use App\Codes\Code;

class PartStatusCode extends Code
{
    protected static $values = [
        'ALTERED'       => 'Altered',
        'AS IS'         => 'As Is',
        'INSPECTED'     => 'Inspected',
        'MANUFACTURED'  => 'Manufactured',
        'MODIFIED'      => 'Modified',
        'NEW'           => 'New',
        'NEW SURPLUS'   => 'New Surplus',
        'OVERHAULED'    => 'Overhauled',
        'PROTOTYPE'     => 'Prototype',
        'REASSEMBLED'   => 'Reassembled',
        'REBUILT'       => 'Rebuilt',
        'REPAIRED'      => 'Repaired',
        'RETREADED'     => 'Retreaded',
        'SERVICEABLE'   => 'Serviceable',
        'TESTED'        => 'Tested',
        'UNSERVICEABLE' => 'Unserviceable'
    ];
    
    protected static $descriptions = [
        'Altered'       => 'The article has been changed from one sound state to another sound state; the aircraft
                            into which the article is expected to be installed must meet the original airworthiness
                            specifications and standards both before and after the modification.',
        
        'As Is'         => 'Any airframe, aircraft engine, propeller, appliance, component part or material, the
                            condition of which cannot certainly and accurately be classified and therefore its status
                            is unknown.',
        
        'Inspected'     => 'Includes testing of products, parts, and appliances. It includes the examination of an
                            item to establish conformity with an approved standard.',
                            
        'Manufactured'  => 'a) The production of a new item in conformity with the applicable design data, or b)
                            Recertification by the original manufacturer after rectification work on an item,
                            previously released under paragraph A, which has been found to be unserviceable prior
                            to entry into service, e.g., defective, in need of inspection or test, or shelf life expired.',
        
        'Modified'      => 'The alteration of an item in conformity with an approved standard.',
        
        'New'           => 'The article described has been newly manufactured.',
        
        'New Surplus'   => 'A product, assembly, accessory, component, part or material produced in conformity
                            with approved data which has been released as surplus by the military, manufacturer,
                            owner-operator, repair facility, etc.; has no operating time cycles and may be
                            accompanied by the manufacturer\'s material certification at the time of sale, and which
                            is being sold by a person other than the original equipment manufacturer.',
        
        'Overhauled'    => 'The article has been disassembled, cleaned, inspected, repaired as necessary,
                            reassembled, and tested in accordance with standards approved by or acceptable to the
                            National Aviation Authority (NAA) / Approving Competent Authority (ACA).',
        
        'Prototype'     => 'An article or appliance submitted to support a type certification program.',
        
        'Reassembled'   => 'Reassembly of an item in conformity with an approved standard, for example reassembly
                            after transportation.',
                
        'Rebuilt'       => 'The article has been disassembled, cleaned, inspected, repaired as necessary,
                            reassembled, and tested to the same tolerances and limits as a new item, using either
                            new parts or used parts that either conform to new part tolerances and limits or to
                            approved oversized or undersized dimensions.',
                            
        'Repaired'      => 'Restoration of a damaged article has been accomplished in such a manner and using
                            material of such quality that its restored condition will be at least equal to its original or
                            properly altered condition (with regard to aerodynamic function, structural strength,
                            resistance to vibration and deterioration, and other qualities affecting airworthiness).',
        
        'Retreaded'     => 'The restoration of a used tire in conformity with an approved standard.',
        
        'Serviceable'   => 'The article is in an airworthy condition.',
        
        'Tested'        => 'Testing of products, parts, and appliances. It includes the examination of an item to
                            establish conformity with an approved standard.',
        
        'Unserviceable' => 'The article is not in an airworthy condition.'
    ];
}
