<?php

namespace App\Interfaces;

interface SPT_SegmentInterface
{
    /*
    |------------------------------------------------------------------------------------------------------------|
    | SPT = Shop Processing Time                                                                                 |
    |------------------------------------------------------------------------------------------------------------|
	| MAH | Shop Total Labor Hours      | Total Labor Hours               | N   | Decimal     | 8,2     | 110.00 |
	| FLW | Shop Flow Time              | Shop Flow Time                  | N   | Integer     | 1/9     |        |
	| MST | Shop Turn Around Time       | Mean Shop Processing Time       | N   | Integer     | 1/4     |        |
	|------------------------------------------------------------------------------------------------------------|
    */
    
    /**
     * Get the Shop Total Labor Hours.
     *
     * @return float
     */
    public function get_SPT_MAH();
    
    /**
     * Get the Shop Flow Time.
     *
     * @return integer
     */
    public function get_SPT_FLW();
    
    /**
     * Get the Shop Turn Around Time.
     *
     * @return integer
     */
    public function get_SPT_MST();
}