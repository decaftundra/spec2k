<?php

namespace App\Http\Middleware;

use App\Alert;
use Closure;
use Illuminate\Support\Facades\Cache;
use App\Interfaces\RCS_SegmentInterface;
use App\PieceParts\PiecePartDetail;
use Illuminate\Support\Facades\View;

class GetCachedInvalidSegment
{
    protected $notificationId;
    protected $piecePartDetailId;
    protected $segment;
    protected $cached;
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $segment)
    {
        $this->segment = $segment;
        
        $notification = $request->route('notification'); // What if notification has been deleted???
        
        if ($notification) {
            $this->setNotificationId($notification);
        }
        
        if ($request->route('piece_part_detail_id')) {
            $this->piecePartDetailId = $request->route('piece_part_detail_id');
        }
        
        $this->setCached();
        
        if (!empty($this->cached)) {
            $request->session()->flash('partial_save', 'This segment has been partially saved.');
            
            // Null values are ignored. No current way round this but probably desired behaviour.
            $request->merge($this->cached)->flash();
        } else {
            $request->session()->forget('partial_save');
            $request->session()->forget('cached');
        }
        
        return $next($request);
    }
    
    /**
     * Comparison callback function.
     * Removes carriage returns and new lines from strings for more reliable comparisons.
     *
     * @param (mixed) $val1
     * @param (mixed) $val2
     * @return
     */
    public function compare($val1, $val2) {
        if (is_string($val1) && is_string($val2)) {
            $val1 = str_replace("\r", "", $val1);
            $val1 = str_replace("\n", "", $val1);
            $val2 = str_replace("\r", "", $val2);
            $val2 = str_replace("\n", "", $val2);
        }
        
        if ($val1 === $val2) {
            return 0;
        }
        
        return ($val1 > $val2)? 1:-1;
    }
    
    /**
     * Set the notification id.
     *
     * @param App\Interfaces\RCS_SegmentInterface $notification
     * @return void
     */
    private function setNotificationId(RCS_SegmentInterface $notification)
    {
        $this->notificationId = $notification->get_RCS_SFI();
    }
    
    /**
     * Retrieve the data from the cache and set the cached property.
     *
     * @return void
     */
    private function setCached()
    {
        if ($this->isPiecePartSegment() && $this->piecePartDetailId) {
            $cachedData = Cache::get($this->piecePartDetailId . '.' . $this->segment);
        } else if ($this->notificationId && !$this->isPiecePartSegment()) {
            $cachedData = Cache::get($this->notificationId . '.' . $this->segment);
        }
        
        if (!empty($cachedData) && isset($cachedData['source_data'])) {
            $sourceData = json_decode($cachedData['source_data'], true);
            
            unset($cachedData['source_data']);
            
            // Remove keys not present in the source data.
            if (is_array($cachedData) && count($cachedData)) {
                foreach ($cachedData as $key => $value) {
                    if (!array_key_exists($key, $sourceData)) {
                        unset($cachedData[$key]);
                    }
                }
            }
            
            // Extract only values that are not empty and different to source data.
            $cachedData = array_filter(array_udiff_assoc($cachedData, $sourceData, [$this, 'compare']));
            $this->cached = $cachedData;
        }
    }
    
    /**
     * Is the segment a piece part segment.
     *
     * @return boolean
     */
    private function isPiecePartSegment()
    {
        return in_array($this->segment, ['WPS_Segment', 'NHS_Segment', 'RPS_Segment']);
    }
}
