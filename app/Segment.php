<?php

namespace App;

use Log;
use Carbon\Carbon;
use App\ValidationProfiler;
use App\Events\SegmentSaving;
use App\Events\SegmentCreated;
use App\Events\SegmentUpdated;
use App\Events\SegmentDeleted;
use App\Traits\RecordActivityTrait;
use App\Interfaces\SegmentInterface;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use App\Interfaces\RecordableInterface;

abstract class Segment extends Model implements RecordableInterface, SegmentInterface
{
    use RecordActivityTrait;
    
    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'saving' => SegmentSaving::class, // No listeners are currently attached to this.
        'created' => SegmentCreated::class,
        'updated' => SegmentUpdated::class,
        'deleted' => SegmentDeleted::class,
    ];
    
    /**
     * Array of validation error messages.
     *
     * @var array
     */
    public $validationErrors = [];
    
    /**
     * Get the segment function prefix.
     *
     * @return string
     */
    public abstract function getPrefix();
    
    /**
     * Get an array of all segment keys.
     *
     * @return array
     */
    public abstract static function getKeys();
    
    /**
     * Get the segment unique identifier.
     * This could be the shop finding id or the piece part detail id.
     *
     * @return string
     */
    public abstract function getIdentifier();
    
    /**
     * Get the segment shop finding id.
     *
     * @return string
     */
    public abstract function getShopFindingId();
    
    /**
	 * Is the segment mandatory.
	 *
	 * @param (integer) $id - shop finding id or piece part detail id
	 * @return boolean
	 */
    public static abstract function isMandatory($id);
    
    /**
     * Is the segment validated.
     * @param (integer) $id - shop finding id or piece part detail id
     *
     * @return true|false|null
     */
    public static abstract function isValid($id);
    
    /**
     * Create or update the segment.
     *
     * @param (array) $data
     * @param (string) $shopFindingId
     * @return void
     */
    public static abstract function createOrUpdateSegment(array $data, string $shopFindingId, $autosave = null);
    
    /**
     * Force the updated model event for when segments are saved in batches.
     *
     * @return void
     */
    public function forceUpdatedEvent() {
        $this->fireModelEvent('updated');
    }
    
    /**
     * Force the updated model event for when segments are saved in batches.
     *
     * @return void
     */
    public function forceSavingEvent() {
        $this->fireModelEvent('saving');
    }
    
    /**
     * Force the updated model event for when segments are saved in batches.
     *
     * @return void
     */
    public function forceCreatedEvent() {
        $this->fireModelEvent('created');
    }
    
    /**
     * Set the is_valid property.
     *
     * @param  string $event
     * @return void
     */
    public function setIsValid($event = NULL)
    {
        // getting the dispatcher instance (needed to enable again the event observer later on)
        $dispatcher = self::getEventDispatcher();
        
        // disabling the events
        self::unsetEventDispatcher();
        
        // perform the operation you want
        $this->is_valid = $this->validate();
        $this->validated_at = Carbon::now();
        
        if (\App::environment('local') || \App::environment('testing')) {
            //Log::info('Event triggered: '.$event);
            //Log::info('setting validation on ' . get_class($this), [$this->getAttributes()]);
        }
        
        $this->save();
        
        // enabling the event dispatcher
        self::setEventDispatcher($dispatcher);
    }
    
    /**
     * Get an array of filled attributes that have been retrieved by the segment methods.
     *
     * @return array
     */
    public function getTreatedAttributes()
    {
        $attributes = $this->getAttributes();
        
        $data = [];
        
        foreach ($attributes as $key => $val) {
            $methodName = $this->getPrefix().$key;
            
            if (method_exists($this, $methodName)) {
                $data[$key] = $this->$methodName();
            }
        }
        
        return $data;
    }
    
    /**
     * Validate the segment.
     *
     * @return boolean
     */
    public abstract function validate();
    
    /**
     * Get the is_valid property value.
     *
     * @return bool
     */
    public function getIsValid() {
        return (bool) $this->is_valid;
    }
}