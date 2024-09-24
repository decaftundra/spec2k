<?php
    
namespace App\Traits;

trait RecordActivityTrait
{
    protected static function bootRecordActivityTrait()
    {
        foreach (static::getModelEvents() as $event) {
            static::$event(function(\App\Interfaces\RecordableInterface $model) use ($event) {
                $model->recordActivity($event);
            });
        }
    }
    
    /**
     * Get the default model events we want to record in the activities table.
     *
     * @return array
     */
    protected static function getModelEvents()
    {
        if (isset(static::$recordsEvents)) {
            return static::$recordsEvents;
        }
        
        return ['created', 'updated', 'deleted'];
    }
    
    /**
     * Create activity record.
     *
     * @param (string) $event
     * @return void
     */
    public function recordActivity($event)
    {
        if (auth()->check()) {
            \App\Activity::create([
                'subject_id' => $this->id,
                'subject_type' => get_class($this),
                'shop_finding_id' => ($this instanceof \App\Segment) ? $this->getShopFindingId() : NULL,
                'name' => $event,
                'user_id' => auth()->user()->id,
            ]);
        }
    }
}