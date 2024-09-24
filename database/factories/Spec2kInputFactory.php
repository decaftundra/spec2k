<?php

use App\Spec2kInput;
use Faker\Generator as Faker;
use App\Codes\PrimaryPiecePartFailureIndicator;

$factory->define(Spec2kInput::class, function (Faker $faker) {
    return [
        'key'           => 'EMS',
        'title'         => 'Engine/APU Module Serial Number',
        'required'      => false,
        'input_type'    => 'text',
        'data_type'     => 'string',
        'min'           => 1,
        'max'           => 20,
        'placeholder'   => '',
        'function'      => 'get_EID_EMS',
        'input_width'   => 'col-sm-6 col-md-4',
        'order'         => 30
    ];
});

$factory->state(Spec2kInput::class, 'text', function (Faker $faker) {
    return [
        'key'           => 'EMS',
        'title'         => 'Engine/APU Module Serial Number',
        'required'      => false,
        'input_type'    => 'text',
        'data_type'     => 'string',
        'min'           => 1,
        'max'           => 20,
        'placeholder'   => '',
        'function'      => 'get_EID_EMS',
        'input_width'   => 'col-sm-6 col-md-4',
        'order'         => 30
    ];
});

$factory->state(Spec2kInput::class, 'textarea', function (Faker $faker) {
    return [
        'key'           => 'PML',
        'title'         => 'Part Modification Level',
        'required'      => false,
        'input_type'    => 'textarea',
        'data_type'     => 'string',
        'min'           => 1,
        'max'           => 1000,
        'function'      => 'get_RCS_PML',
        'input_width'   => 'col-sm-12 col-md-12',
        'order'         => 300
    ];
});

$factory->state(Spec2kInput::class, 'select', function (Faker $faker) {
    return [
        'key'           => 'PFC',
        'title'         => 'Primary Piece Part Failure Indicator',
        'required'      => true,
        'input_type'    => 'select',
        'data_type'     => 'string',
        'options'       => PrimaryPiecePartFailureIndicator::getDropDownValues(),
        'function'      => 'get_WPS_PFC',
        'input_width'   => 'col-sm-6 col-md-6',
        'order'         => 30,
        'display'       => true,
        'default'       => 'D'
    ];
});

$factory->state(Spec2kInput::class, 'radio', function (Faker $faker) {
    return [
        'key'           => 'RFI',
        'title'         => 'Repair Final Action Indicator',
        'required'      => true,
        'input_type'    => 'radio',
        'data_type'     => 'boolean',
        'function'      => 'get_SAS_RFI',
        'input_width'   => 'col-sm-6 col-md-6',
        'order'         => 60,
        'options'       => [1 => 'Yes', 0 => 'No'],
        'description'   => 'Shop returning the part certified back to service.',
        'display'       => true
    ];
});

$factory->state(Spec2kInput::class, 'number', function (Faker $faker) {
    return [
        'key'           => 'ATC',
        'title'         => 'APU Cumulative Total Cycles',
        'required'      => false,
        'input_type'    => 'number',
        'data_type'     => 'integer',
        'min'           => 1,
        'function'      => 'get_API_ATC',
        'input_width'   => 'col-sm-6 col-md-4',
        'order'         => 60
    ];
});

$factory->state(Spec2kInput::class, 'hidden', function (Faker $faker) {
    return [
        'key'           => 'PPI',
        'title'         => 'Piece Part Record Identifier',
        'required'      => true,
        'input_type'    => 'hidden',
        'data_type'     => 'string',
        'min'           => 1,
        'max'           => 50,
        'function'      => 'get_WPS_PPI',
        'input_width'   => 'col-sm-6 col-md-4',
        'order'         => 20,
        'display'       => true
    ];
});

$factory->state(Spec2kInput::class, 'date', function (Faker $faker) {
    return [
        'key'           => 'MRD',
        'title'         => 'Material Receipt Date',
        'required'      => false,
        'input_type'    => 'date',
        'data_type'     => 'string',
        'function'      => 'get_WPS_MRD',
        'input_width'   => 'col-sm-6 col-md-4',
        'order'         => 140,
        'placeholder'   => 'dd/mm/yyyy'
    ];
});

$factory->state(Spec2kInput::class, 'checkbox', function (Faker $faker) {
    return [
        'key'           => 'RFI',
        'title'         => 'Repair Final Action Indicator',
        'required'      => true,
        'input_type'    => 'checkbox',
        'data_type'     => 'boolean',
        'function'      => 'get_SAS_RFI',
        'input_width'   => 'col-sm-6 col-md-6',
        'order'         => 60,
        'description'   => 'Shop returning the part certified back to service.',
        'display'       => true
    ];
});

$factory->state(Spec2kInput::class, 'radio-pp', function (Faker $faker) {
    return [
        'key'           => 'PFC',
        'title'         => 'Primary Piece Part Failure Indicator',
        'required'      => true,
        'input_type'    => 'radio-pp',
        'data_type'     => 'string',
        'options'       => PrimaryPiecePartFailureIndicator::getDropDownValues(false),
        'function'      => 'get_WPS_PFC',
        'input_width'   => 'col-sm-6 col-md-6',
        'order'         => 30,
        'display'       => true,
        'default'       => 'D'
    ];
});