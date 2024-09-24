<?php

namespace App\Interfaces;

interface RecordInterface
{
    /**
     * Get the author id.
     *
     * @return string
     */
    public function get_ATA_AuthorId();
    
    /**
     * Get the author version.
     *
     * @return integer
     */
    public function get_ATA_AuthorVersion();
    
    /**
     * Get the shop findings version.
     *
     * @return integer
     */
    public function get_SF_Version();
    
    /**
     * Get the record status.
     *
     * @return string
     */
    public function getStatus();
    
    /**
     * Get the date when the status was recorded.
     *
     * @return string
     */
    public function getStatusDate();
    
    /**
     * Set the status on the record.
     *
     * @param (string) $status
     * @return mixed
     */
    public function setStatus($status);
}