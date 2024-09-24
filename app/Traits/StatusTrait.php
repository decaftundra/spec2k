<?php

namespace App\Traits;

trait StatusTrait
{
    /**
     * The array of statuses.
     *
     * @var array
     */
    public static $statuses = [
        'in_progress' => 'In Progress',
        'subcontracted' => 'Subcontracted',
        'complete_scrapped' => 'Complete & Scrapped',
        'complete_shipped' => 'Complete & Shipped'
    ];
    
    /**
     * Get the record status nice name.
     *
     * @return string
     */
    public function getStatus()
    {
        return array_key_exists($this->status, self::$statuses) ? self::$statuses[$this->status] : self::$statuses['in_progress'];
    }
    
    /**
     * Set the status on the record.
     *
     * @param (string) $status
     * @return mixed
     */
    public function setStatus($status, $statusDate = NULL)
    {
        // Ignore any records that are already complete to avoid unwanted 'return to stock' updates.
        if (($this->status != 'complete_shipped') && ($this->status != 'complete_scrapped')) {
            
            if (array_key_exists($status, self::$statuses)) {
                $this->status = $status;
                
                if ($status == 'subcontracted') {
                    $this->subcontracted_at = $statusDate ?? date('Y-m-d H:i:s');
                }
                
                if ($status == 'complete_scrapped') {
                    $this->scrapped_at = $statusDate ?? date('Y-m-d H:i:s');
                }
                
                if ($status == 'complete_shipped') {
                    $this->shipped_at = $statusDate ?? date('Y-m-d H:i:s');
                }
                
                return $this->save();
            }
        }
    }
    
    /**
     * Get the date when the status was recorded.
     *
     * @return string
     */
    public function getStatusDate()
    {
        if ($this->status == 'in_progress') return NULL;
        
        if ($this->status == 'subcontracted') return $this->subcontracted_at;
        
        if ($this->status == 'complete_scrapped') return $this->scrapped_at;
        
        if ($this->status == 'complete_shipped') return $this->shipped_at;
        
        return NULL;
    }
    
    /**
     * Put item on standby.
     *
     * @return mixed
     */
    public function putOnStandby()
    {
        $this->standby_at = date('Y-m-d H:i:s');
        return $this->save();
    }
    
    /**
     * Remove item from on standby.
     *
     * @return mixed
     */
    public function removeOnStandby()
    {
        $this->standby_at = NULL;
        return $this->save();
    }
}