<?php

namespace App\ShopFindings;

use App\User;
use App\Activity;
use App\Location;
use App\PartList;
use App\UtasCode;
use Carbon\Carbon;
use App\HDR_Segment;
use App\Codes\Airline;
use App\Traits\StatusTrait;
use App\PieceParts\PiecePart;
use App\PieceParts\WPS_Segment;
use App\PieceParts\NHS_Segment;
use App\PieceParts\RPS_Segment;
use App\ShopFindings\AID_Segment;
use App\ShopFindings\EID_Segment;
use App\ShopFindings\API_Segment;
use App\ShopFindings\RCS_Segment;
use App\ShopFindings\SAS_Segment;
use App\ShopFindings\SUS_Segment;
use App\ShopFindings\RLS_Segment;
use App\ShopFindings\LNK_Segment;
use App\ShopFindings\ATT_Segment;
use App\ShopFindings\SPT_Segment;
use App\Events\ShopFindingCreated;
use App\Interfaces\RecordInterface;
use App\Notification;
use DateTimeInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShopFinding extends Model implements RecordInterface
{
    use StatusTrait, SoftDeletes;
    
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
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'deleted_at',
        'standby_at',
        'subcontracted_at',
        'scrapped_at',
        'shipped_at'
    ];
    
    protected $connection = 'mysql';

    public $incrementing = false;
    
    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'plant_code'];
    
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['is_utas', 'piece_part_count', 'notification_atts'];
    
    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        
        /*
        Whenever a shopfinding is created we need to bring over
        the current corresponding notification status,
        and planner group.
        */
        
        'created' => ShopFindingCreated::Class
    ];
    
    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        
        self::creating(function($model){
            // These values should come from the xml schema really.
            $model->ataID = 'R2009.1';
            $model->ataVersion = 1;
            $model->SFVersion = 2;
        });
        
        // Determine if the user can view all shop findings and restrict accordingly.
        if (auth()->check() && Gate::denies('view-all-notifications')) {
            static::addGlobalScope('permission_to_view', function(Builder $builder){
                $builder->where('plant_code', auth()->user()->location->plant_code)
                    ->orWhereExists(function($query){
                        $query->select('id')
                            ->from('notifications')
                            ->whereRaw('notifications.id = shop_findings.id');
                    });
            });
        }
    }
    
    
    
    
    
    
    /**
     * Get the header information record associated with the spec 2000 report.
     */
    public function HDR_Segment()
    {
        return $this->hasOne('App\HDR_Segment');
    }
    
    /**
     * Get the piece part record associated with the spec 2000 report.
     */
    public function PiecePart()
    {
        return $this->hasOne('App\PieceParts\PiecePart');
    }
    
    /**
     * Get the piece part record associated with the spec 2000 report.
     */
    public function PiecePartDetails()
    {
        return $this->hasManyThrough('App\PieceParts\PiecePartDetail', 'App\PieceParts\PiecePart');
    }
    
    /**
     * Get the shop finding that owns the shop findings detail.
     */
    public function ShopFindingsDetail()
    {
        return $this->hasOne('App\ShopFindings\ShopFindingsDetail');
    }
    
    /**
     * Get the activities that belong to the shop finding.
     */
     public function activities()
     {
         return $this->hasMany('App\Activity');
     }
     
    /**
     * Get the user that belongs to the shop finding.
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'planner_group', 'planner_group');
    }
    
    /**
     * Set the is_valid property.
     *
     * @return void
     */
    public function setIsValid($event = NULL)
    {
        // Getting the dispatcher instance (needed to enable again the event observer later on).
        $dispatcher = self::getEventDispatcher();
        
        // Disabling the events.
        self::unsetEventDispatcher();
        
        // Perform the operation you want.
        $this->is_valid = $this->validate();
        $this->validated_at = Carbon::now();
        
        if (\App::environment('local')) {
            Log::info('Event triggered: '.$event);
            Log::info('setting validation on ' . get_class($this), [$this->getAttributes()]);
        }
        
        $this->save();
        
        // Enabling the event dispatcher.
        self::setEventDispatcher($dispatcher);
    }
    
    /**
     * Validate the shop finding.
     *
     * @param (string) $id
     * @return boolean
     */
    public function validate($id = NULL)
    {
        if ($id) {
            $shopFinding = self::findOrFail($id);
        } else {
            $shopFinding = $this;
        }
        
        if (!$shopFinding->HDR_Segment) return false;
        if (!$shopFinding->ShopFindingsDetail) return false;
        if (!$shopFinding->ShopFindingsDetail->RCS_Segment) return false;
        if (!$shopFinding->ShopFindingsDetail->SAS_Segment) return false;
        
        // Always will be Mandatory.
        if ($shopFinding->HDR_Segment->getIsValid() !== true) return false;
        if ($shopFinding->ShopFindingsDetail->RCS_Segment->getIsValid() !== true) return false;
        if ($shopFinding->ShopFindingsDetail->SAS_Segment->getIsValid() !== true) return false;
        
        $PiecePartDetails = PiecePart::getPiecePartDetails($shopFinding->id);
        
        // If false no piece parts have been saved yet.
        if (count($PiecePartDetails) && !is_object($shopFinding->PiecePartDetails)) return false;
        
        // If false all piece parts have not been saved yet.
        if (count($PiecePartDetails) != count($shopFinding->PiecePartDetails)) return false;
        
        if (is_object($shopFinding->PiecePartDetails) && count($shopFinding->PiecePartDetails)) {
            foreach ($shopFinding->PiecePartDetails as $PiecePartDetail) {
                if (!$PiecePartDetail->WPS_Segment) return false; // Always mandatory.
                if ($PiecePartDetail->WPS_Segment && $PiecePartDetail->WPS_Segment->getIsValid() !== true) return false;
            }
        }
        
        // Need to check if each segment is mandatory in case of custom validation profile.
        if (Misc_Segment::isMandatory($shopFinding->id)) {
            if (Misc_Segment::isValid($shopFinding->id) !== true) return false;
        } else {
            if (Misc_Segment::isValid($shopFinding->id) === false) return false;
        }
        
        if (AID_Segment::isMandatory($shopFinding->id)) {
            if (!$shopFinding->ShopFindingsDetail->AID_Segment) return false;
            if ($shopFinding->ShopFindingsDetail->AID_Segment->getIsValid() !== true) return false;
        } else {
            if ($shopFinding->ShopFindingsDetail->AID_Segment && $shopFinding->ShopFindingsDetail->AID_Segment->getIsValid() === false) return false;
        }
        
        if (EID_Segment::isMandatory($shopFinding->id)) {
            if (!$shopFinding->ShopFindingsDetail->EID_Segment) return false;
            if ($shopFinding->ShopFindingsDetail->EID_Segment->getIsValid() !== true) return false;
        } else {
            if ($shopFinding->ShopFindingsDetail->EID_Segment && $shopFinding->ShopFindingsDetail->EID_Segment->getIsValid() === false) return false;
        }
        
        if (API_Segment::isMandatory($shopFinding->id)) {
            if (!$shopFinding->ShopFindingsDetail->API_Segment) return false;
            if ($shopFinding->ShopFindingsDetail->API_Segment->getIsValid() !== true) return false;
        } else {
            if ($shopFinding->ShopFindingsDetail->API_Segment && $shopFinding->ShopFindingsDetail->API_Segment->getIsValid() === false) return false;
        }
        
        if (SUS_Segment::isMandatory($shopFinding->id)) {
            if (!$shopFinding->ShopFindingsDetail->SUS_Segment) return false;
            if ($shopFinding->ShopFindingsDetail->SUS_Segment->getIsValid() !== true) return false;
        } else {
            if ($shopFinding->ShopFindingsDetail->SUS_Segment && $shopFinding->ShopFindingsDetail->SUS_Segment->getIsValid() === false) return false;
        }
        
        if (RLS_Segment::isMandatory($shopFinding->id)) {
            if (!$shopFinding->ShopFindingsDetail->RLS_Segment) return false;
            if ($shopFinding->ShopFindingsDetail->RLS_Segment->getIsValid() !== true) return false;
        } else {
            if ($shopFinding->ShopFindingsDetail->RLS_Segment && $shopFinding->ShopFindingsDetail->RLS_Segment->getIsValid() === false) return false;
        }
        
        if (LNK_Segment::isMandatory($shopFinding->id)) {
            if (!$shopFinding->ShopFindingsDetail->LNK_Segment) return false;
            if ($shopFinding->ShopFindingsDetail->LNK_Segment->getIsValid() !== true) return false;
        } else {
            if ($shopFinding->ShopFindingsDetail->LNK_Segment && $shopFinding->ShopFindingsDetail->LNK_Segment->getIsValid() === false) return false;
        }
        
        if (ATT_Segment::isMandatory($shopFinding->id)) {
            if (!$shopFinding->ShopFindingsDetail->ATT_Segment) return false;
            if ($shopFinding->ShopFindingsDetail->ATT_Segment->getIsValid() !== true) return false;
        } else {
            if ($shopFinding->ShopFindingsDetail->ATT_Segment && $shopFinding->ShopFindingsDetail->ATT_Segment->getIsValid() === false) return false;
        }
        
        if (SPT_Segment::isMandatory($shopFinding->id)) {
            if (!$shopFinding->ShopFindingsDetail->SPT_Segment) return false;
            if ($shopFinding->ShopFindingsDetail->SPT_Segment->getIsValid() !== true) return false;
        } else {
            if ($shopFinding->ShopFindingsDetail->SPT_Segment && $shopFinding->ShopFindingsDetail->SPT_Segment->getIsValid() === false) return false;
        }
        
        if (is_array($shopFinding->PiecePartDetails) && count($shopFinding->PiecePartDetails)) {
            foreach ($shopFinding->PiecePartDetails as $PiecePartDetail) {
                if (NHS_Segment::isMandatory($PiecePartDetail['id'])) {
                    if ($PiecePartDetail->NHS_Segment && $PiecePartDetail->NHS_Segment->getIsValid() !== true) return false;
                } else {
                    if ($PiecePartDetail->NHS_Segment && $PiecePartDetail->NHS_Segment->getIsValid() === false) return false;
                }
                
                if (RPS_Segment::isMandatory($PiecePartDetail['id'])) {
                    if ($PiecePartDetail->RPS_Segment && $PiecePartDetail->RPS_Segment->getIsValid() !== true) return false;
                } else {
                    if ($PiecePartDetail->RPS_Segment && $PiecePartDetail->RPS_Segment->getIsValid() === false) return false;
                }
            }
        }
        
        return true;
    }
    
    /**
     * Get a validation report for the whole shop finding.
     *
     * @return string
     */
    public function getValidationReport()
    {
        //$report = 'Notification ID: ' . $this->id . "\r\n";
        
        $report = '';
        
        if ($this->isValid()) {
            $report .= 'Valid' . "\r\n";
            
            return $report;
        }
        
        if (!$this->HDR_Segment) {
            $report .= "HDR Segment: The Header segment is not saved.\r\n";
        } else {
            if ($this->HDR_Segment->getIsValid() !== true) {
                if (!$this->HDR_Segment->validate()) {
                    $report .= 'HDR Segment: ' . implode('. ', $this->HDR_Segment->validationErrors) . "\r\n";
                }
            }
        }
        
        if (!$this->ShopFindingsDetail) {
            $report .= "Shop Findings Detail: No Shop Finding Detail segments are saved.\r\n";
        } else {
            if (AID_Segment::isMandatory($this->id)) {
                if (!$this->ShopFindingsDetail->AID_Segment) {
                    $report .= "AID Segment: The Airframe Information segment is not saved.\r\n";
                }
                
                if ($this->ShopFindingsDetail->AID_Segment && $this->ShopFindingsDetail->AID_Segment->getIsValid() !== true) {
                    if (!$this->ShopFindingsDetail->AID_Segment->validate()) {
                        $report .= 'AID Segment: ' . implode('. ', $this->ShopFindingsDetail->AID_Segment->validationErrors) . "\r\n";
                    }
                }
            } else {
                if ($this->ShopFindingsDetail->AID_Segment && $this->ShopFindingsDetail->AID_Segment->getIsValid() === false) {
                    if (!$this->ShopFindingsDetail->AID_Segment->validate()) {
                        $report .= 'AID Segment: ' . implode('. ', $this->ShopFindingsDetail->AID_Segment->validationErrors) . "\r\n";
                    }
                }
            }
            
            if (EID_Segment::isMandatory($this->id)) {
                if (!$this->ShopFindingsDetail->EID_Segment) {
                    $report .= "EID Segment: The Engine Information segment is not saved.\r\n";
                }
                
                if ($this->ShopFindingsDetail->EID_Segment && $this->ShopFindingsDetail->EID_Segment->getIsValid() !== true) {
                    if (!$this->ShopFindingsDetail->EID_Segment->validate()) {
                        $report .= 'EID Segment: ' . implode('. ', $this->ShopFindingsDetail->EID_Segment->validationErrors) . "\r\n";
                    }
                }
            } else {
                if ($this->ShopFindingsDetail->EID_Segment && $this->ShopFindingsDetail->EID_Segment->getIsValid() === false) {
                    if (!$this->ShopFindingsDetail->EID_Segment->validate()) {
                        $report .= 'EID Segment: ' . implode('. ', $this->ShopFindingsDetail->EID_Segment->validationErrors) . "\r\n";
                    }
                }
            }
            
            if (API_Segment::isMandatory($this->id)) {
                if (!$this->ShopFindingsDetail->API_Segment) {
                    $report .= "API Segment: The APU Information segment is not saved.\r\n";
                }
                
                if ($this->ShopFindingsDetail->API_Segment && $this->ShopFindingsDetail->API_Segment->getIsValid() !== true) {
                    if (!$this->ShopFindingsDetail->API_Segment->validate()) {
                        $report .= 'API Segment: ' . implode('. ', $this->ShopFindingsDetail->API_Segment->validationErrors) . "\r\n";
                    }
                }
            } else {
                if ($this->ShopFindingsDetail->API_Segment && $this->ShopFindingsDetail->API_Segment->getIsValid() === false) {
                    if (!$this->ShopFindingsDetail->API_Segment->validate()) {
                        $report .= 'API Segment: ' . implode('. ', $this->ShopFindingsDetail->API_Segment->validationErrors) . "\r\n";
                    }
                }
            }
            
            if (!$this->ShopFindingsDetail->RCS_Segment) {
                $report .= "RCS Segment: The Received LRU segment is not saved.\r\n";
            } else {
                if ($this->ShopFindingsDetail->RCS_Segment->getIsValid() !== true) {
                    if (!$this->ShopFindingsDetail->RCS_Segment->validate()) {
                        $report .= 'RCS Segment: ' . implode('. ', $this->ShopFindingsDetail->RCS_Segment->validationErrors) . "\r\n";
                    }
                }
            }
            
            if (!$this->ShopFindingsDetail->SAS_Segment) {
                $report .= "SAS Segment: The Shop Action Details segment is not saved.\r\n";
            } else {
                if ($this->ShopFindingsDetail->SAS_Segment->getIsValid() !== true) {
                    if (!$this->ShopFindingsDetail->SAS_Segment->validate()) {
                        $report .= 'SAS Segment: ' . implode('. ', $this->ShopFindingsDetail->SAS_Segment->validationErrors) . "\r\n";
                    }
                }
            }
            
            if (SUS_Segment::isMandatory($this->id)) {
                if (!$this->ShopFindingsDetail->SUS_Segment) {
                    $report .= "SUS Segment: The Shipped LRU segment is not saved.\r\n";
                }
                
                if ($this->ShopFindingsDetail->SUS_Segment && $this->ShopFindingsDetail->SUS_Segment->getIsValid() !== true) {
                    if (!$this->ShopFindingsDetail->SUS_Segment->validate()) {
                        $report .= 'SUS Segment: ' . implode('. ', $this->ShopFindingsDetail->SUS_Segment->validationErrors) . "\r\n";
                    }
                }
            } else {
                if ($this->ShopFindingsDetail->SUS_Segment && $this->ShopFindingsDetail->SUS_Segment->getIsValid() === false) {
                    if (!$this->ShopFindingsDetail->SUS_Segment->validate()) {
                        $report .= 'SUS Segment: ' . implode('. ', $this->ShopFindingsDetail->SUS_Segment->validationErrors) . "\r\n";
                    }
                }
            }
            
            if (RLS_Segment::isMandatory($this->id)) {
                if (!$this->ShopFindingsDetail->RLS_Segment) {
                    $report .= "RLS Segment: The Removed LRU segment is not saved.\r\n";
                }
                
                if ($this->ShopFindingsDetail->RLS_Segment && $this->ShopFindingsDetail->RLS_Segment->getIsValid() !== true) {
                    if (!$this->ShopFindingsDetail->RLS_Segment->validate()) {
                        $report .= 'RLS Segment: ' . implode('. ', $this->ShopFindingsDetail->RLS_Segment->validationErrors) . "\r\n";
                    }
                }
            } else {
                if ($this->ShopFindingsDetail->RLS_Segment && $this->ShopFindingsDetail->RLS_Segment->getIsValid() === false) {
                    if (!$this->ShopFindingsDetail->RLS_Segment->validate()) {
                        $report .= 'RLS Segment: ' . implode('. ', $this->ShopFindingsDetail->RLS_Segment->validationErrors) . "\r\n";
                    }
                }
            }
            
            if (LNK_Segment::isMandatory($this->id)) {
                if (!$this->ShopFindingsDetail->LNK_Segment) {
                    $report .= "LNK Segment: The Linking Fields segment is not saved.\r\n";
                }
                
                if ($this->ShopFindingsDetail->LNK_Segment && $this->ShopFindingsDetail->LNK_Segment->getIsValid() !== true) {
                    if (!$this->ShopFindingsDetail->LNK_Segment->validate()) {
                        $report .= 'LNK Segment: ' . implode('. ', $this->ShopFindingsDetail->LNK_Segment->validationErrors) . "\r\n";
                    }
                }
            } else {
                if ($this->ShopFindingsDetail->LNK_Segment && $this->ShopFindingsDetail->LNK_Segment->getIsValid() === false) {
                    if (!$this->ShopFindingsDetail->LNK_Segment->validate()) {
                        $report .= 'LNK Segment: ' . implode('. ', $this->ShopFindingsDetail->LNK_Segment->validationErrors) . "\r\n";
                    }
                }
            }
            
            if (ATT_Segment::isMandatory($this->id)) {
                if (!$this->ShopFindingsDetail->ATT_Segment) {
                    $report .= "ATT Segment: The Accumulated Time Text segment is not saved.\r\n";
                }
                
                if ($this->ShopFindingsDetail->ATT_Segment && $this->ShopFindingsDetail->ATT_Segment->getIsValid() !== true) {
                    if (!$this->ShopFindingsDetail->ATT_Segment->validate()) {
                        $report .= 'ATT Segment: ' . implode('. ', $this->ShopFindingsDetail->ATT_Segment->validationErrors) . "\r\n";
                    }
                }
            } else {
                if ($this->ShopFindingsDetail->ATT_Segment && $this->ShopFindingsDetail->ATT_Segment->getIsValid() === false) {
                    if (!$this->ShopFindingsDetail->ATT_Segment->validate()) {
                        $report .= 'ATT Segment: ' . implode('. ', $this->ShopFindingsDetail->ATT_Segment->validationErrors) . "\r\n";
                    }
                }
            }
            
            if (SPT_Segment::isMandatory($this->id)) {
                if (!$this->ShopFindingsDetail->SPT_Segment) {
                    $report .= "SPT Segment: The Shop Processing Time segment is not saved.\r\n";
                }
                
                if ($this->ShopFindingsDetail->SPT_Segment && $this->ShopFindingsDetail->SPT_Segment->getIsValid() !== true) {
                    if (!$this->ShopFindingsDetail->SPT_Segment->validate()) {
                        $report .= 'SPT Segment: ' . implode('. ', $this->ShopFindingsDetail->SPT_Segment->validationErrors) . "\r\n";
                    }
                }
            } else {
                if ($this->ShopFindingsDetail->SPT_Segment && $this->ShopFindingsDetail->SPT_Segment->getIsValid() === false) {
                    if (!$this->ShopFindingsDetail->SPT_Segment->validate()) {
                        $report .= 'SPT Segment: ' . implode('. ', $this->ShopFindingsDetail->SPT_Segment->validationErrors) . "\r\n";
                    }
                }
            }
            
            // Need to check if each segment is mandatory in case of custom validation profile.
            if (Misc_Segment::isMandatory($this->id)) {
                if (!$this->ShopFindingsDetail->Misc_Segment) {
                    $segmentName = Misc_Segment::getName($this->id);
                    $report .= "Misc. Segment: The $segmentName segment is not saved.\r\n";
                }
                
                if (Misc_Segment::isValid($this->id) !== true) {
                    if ($this->ShopFindingsDetail->Misc_Segment && !$this->ShopFindingsDetail->Misc_Segment->validate()) {
                        $report .= 'Misc. Segment: ' . implode('. ', $this->ShopFindingsDetail->Misc_Segment->validationErrors) . "\r\n";
                    }
                }
            } else {
                if (Misc_Segment::isValid($this->id) === false) {
                    if ($this->ShopFindingsDetail->Misc_Segment && !$this->ShopFindingsDetail->Misc_Segment->validate()) {
                        $report .= 'Misc. Segment: ' . implode('. ', $this->ShopFindingsDetail->Misc_Segment->validationErrors) . "\r\n";
                    }
                }
            }
        }
        
        $PiecePartDetails = PiecePart::getPiecePartDetails($this->id);
        
        $missingWPS_Segment = false; // Set flag to avoid multiple occurrences of the same message.
        
        // If false no piece parts have been saved yet.
        if (!empty($PiecePartDetails) && count($PiecePartDetails) && !count($this->PiecePartDetails)) {
            $report .= "Piece Part Details: No Piece Part Detail segments are saved.\r\n";
        } else if (count($this->PiecePartDetails) && (count($PiecePartDetails) != count($this->PiecePartDetails))) { // If false all piece parts have not been saved yet.
            if (!$missingWPS_Segment) {
                $report .= "Piece Part Details: Not all mandatory Piece Part Detail segments are saved yet.\r\n";
                $missingWPS_Segment = true;
            }
        }
        
        if (is_object($this->PiecePartDetails) && count($this->PiecePartDetails)) {
            
            // Get Piece Part Warning Message if exists.
            $warningMessage = PiecePart::getPiecePartWarningMessage($this->id);
            
            if ($warningMessage) {
                $report .= $warningMessage . "\r\n";
            }
            
            foreach ($this->PiecePartDetails as $PiecePartDetail) {
                
                // This will trigger if there are piece part segments saved but no wps segment.
                if (!$PiecePartDetail->WPS_Segment) {
                    if (!$missingWPS_Segment) {
                        $report .= "Piece Part Details: Not all mandatory Piece Part Detail segments are saved yet.\r\n";
                        $missingWPS_Segment = true;
                    }
                }
                
                if ($PiecePartDetail->WPS_Segment && $PiecePartDetail->WPS_Segment->getIsValid() !== true) {
                    if (!$PiecePartDetail->WPS_Segment->validate()) {
                        $report .= 'WPS Segment:'.$PiecePartDetail->id.': '.implode('. ', $PiecePartDetail->WPS_Segment->validationErrors)."\r\n";
                    }
                };
                
                if (NHS_Segment::isMandatory($PiecePartDetail['id'])) {
                    if (!$PiecePartDetail->NHS_Segment) {
                        $report .= "NHS Segment:{$PiecePartDetail->id}: The Next Higher Assembly segment is not saved.\r\n";
                    }
                    
                    if ($PiecePartDetail->NHS_Segment && $PiecePartDetail->NHS_Segment->getIsValid() !== true) {
                        if (!$PiecePartDetail->NHS_Segment->validate()) {
                            $report .= 'NHS Segment:'.$PiecePartDetail->id.': '.implode('. ', $PiecePartDetail->NHS_Segment->validationErrors)."\r\n";
                        }
                    }
                } else {
                    if ($PiecePartDetail->NHS_Segment && $PiecePartDetail->NHS_Segment->getIsValid() === false) {
                        if (!$PiecePartDetail->NHS_Segment->validate()) {
                            $report .= 'NHS Segment:'.$PiecePartDetail->id.': '.implode('. ', $PiecePartDetail->NHS_Segment->validationErrors)."\r\n";
                        }
                    }
                }
                
                if (RPS_Segment::isMandatory($PiecePartDetail['id'])) {
                    if (!$PiecePartDetail->RPS_Segment) {
                        $report .= "RPS Segment:{$PiecePartDetail->id}: The Replaced Piece Part segment is not saved.\r\n";
                    }
                    
                    if ($PiecePartDetail->RPS_Segment && $PiecePartDetail->RPS_Segment->getIsValid() !== true) {
                        if (!$PiecePartDetail->RPS_Segment->validate()) {
                            $report .= 'RPS Segment:'.$PiecePartDetail->id.': '.implode('. ', $PiecePartDetail->RPS_Segment->validationErrors)."\r\n";
                        }
                    }
                } else {
                    if ($PiecePartDetail->RPS_Segment && $PiecePartDetail->RPS_Segment->getIsValid() === false) {
                        if (!$PiecePartDetail->RPS_Segment->validate()) {
                            $report .= 'RPS Segment:'.$PiecePartDetail->id.': '.implode('. ', $PiecePartDetail->RPS_Segment->validationErrors)."\r\n";
                        }
                    }
                }
            }
        }
        
        return $report;
    }
    
    /**
     * Is the shop finding and related spec 2k valid?
     *
     * @return boolean
     */
    public function isValid($id = NULL)
    {
        if ($id) {
            $shopFinding = self::findOrFail($id);
        } else {
            $shopFinding = $this;
        }
        
        return $shopFinding->is_valid;
    }
    
    /**
     * Are all the notification piece parts valid.
     *
     * @param (string) $id - Notification ID
     * @return boolean
     */
    public static function arePiecePartsValid($id)
    {
        $PiecePartDetails = PiecePart::getPiecePartDetails($id);
        
        if (is_array($PiecePartDetails) && count($PiecePartDetails)) {
            foreach ($PiecePartDetails as $PiecePartDetail) {
                if (WPS_Segment::isValid($PiecePartDetail['id']) !== true) return false;
                
                if (NHS_Segment::isMandatory($PiecePartDetail['id'])) {
                    if (NHS_Segment::isValid($PiecePartDetail['id']) !== true) return false;
                } else {
                    if (NHS_Segment::isValid($PiecePartDetail['id']) === false) return false;
                }
                
                if (RPS_Segment::isMandatory($PiecePartDetail['id'])) {
                    if (RPS_Segment::isValid($PiecePartDetail['id']) !== true) return false;
                } else {
                    if (RPS_Segment::isValid($PiecePartDetail['id']) === false) return false;
                }
            }
        }
        
        return true;
    }
    
    /**
     * Pull in some attributes needed for datases index page which may not be saved yet.
     *
     * @return array
     */
    public function getNotificationAttsAttribute()
    {
        if (!$this->HDR_Segment || !$this->ShopFindingsDetail || !$this->ShopFindingsDetail->RCS_Segment) {
            $notification = Notification::find($this->id);
        
            return [
                'RCS_MPN' => $notification ? $notification->get_RCS_MPN() : NULL,
                'RCS_SER' => $notification ? $notification->get_RCS_SER() : NULL,
                'HDR_ROC' => $notification ? $notification->get_HDR_ROC() : NULL,
                'HDR_RON' => $notification ? $notification->get_HDR_RON() : NULL,
                'RCS_MRD' => $notification ? $notification->get_RCS_MRD() : NULL,
                'SUS_SER' => $notification ? $notification->get_SUS_SER() : NULL,
            ];
        }
        
        return [];
    }
    
    /**
     * Is this a UTAS part.
     *
     * @return boolean
     */
    public function getIsUtasAttribute()
    {
        $this->load('ShopFindingsDetail.RCS_Segment');
        
        if ($this->ShopFindingsDetail && $this->ShopFindingsDetail->RCS_Segment) {
            $partNo = $this->ShopFindingsDetail->RCS_Segment->get_RCS_MPN();
        } else {
            if (Cache::has('notification.'.$this->id)) {
                $notification = Cache::get('notification.'.$this->id);
            } else {
                $notification = Notification::where('rcsSFI', $this->id)->first();
                
                Cache::put('notification.'.$this->id, $notification, 3660);
            }
            
            if (!$notification) return false;
            
            $partNo = $notification->get_RCS_MPN();
        }
        
        if (!$partNo) return false;
        
        $codes = UtasCode::getAllUtasCodes();
        
        return in_array($partNo, $codes);
    }
    
    /**
     * Get the number of piece parts.
     *
     * @return integer
     */
    public function getPiecePartCountAttribute()
    {
        $this->load('PiecePartDetails');
        
        $noOfSavedPieceParts = $this->PiecePartDetails? $this->PiecePartDetails->count() : 0;
        
        $notification = Notification::withCount('pieceParts')->where('rcsSFI', $this->id)->first();
        
        $notificationPieceParts = (!$notification || !$notification->PieceParts) ? 0 : $notification->piece_parts_count;
        
        return max($notificationPieceParts, $noOfSavedPieceParts);
    }
    
    /**
     * Get the sql query for the latest activity user acronym.
     *
     * @return string
     */
    private static function getActivitiesQuery()
    {
        return "SELECT SUBSTRING_INDEX(GROUP_CONCAT(users.acronym ORDER BY activities.created_at DESC), ',', 1) as acronym, activities.shop_finding_id
                FROM activities
                LEFT JOIN users
                ON activities.user_id = users.id
                GROUP BY activities.shop_finding_id";
    }
    
    /**
     * Get the sql query for the shop finding piece parts count.
     *
     * @return string
     */
    private static function getShopFindingPiecePartsCountQuery()
    {
        return "SELECT piece_parts.shop_finding_id, COUNT(piece_part_details.id) as piece_part_count
                FROM piece_parts
                LEFT JOIN piece_part_details
                ON piece_parts.id = piece_part_details.piece_part_id
                WHERE piece_part_details.deleted_at IS NULL
                GROUP BY piece_parts.shop_finding_id";
    }
    
    /**
     * Get the sql query for the notification piece parts count.
     *
     * @return string
     */
    private static function getNotificationPiecePartsCountQuery()
    {
        return "SELECT notification_piece_parts.notification_id, COUNT(notification_piece_parts.id) as piece_part_count
                FROM notification_piece_parts
                WHERE EXISTS (SELECT id FROM shop_findings WHERE shop_findings.id = notification_piece_parts.notification_id)
                AND notification_piece_parts.deleted_at IS NULL
                GROUP BY notification_piece_parts.notification_id";
    }
    
    /**
     * Get a filtered collection of datasets.
     *
     * @param (string) $filter
     * @param (string) $search
     * @param (string) $dateStart
     * @param (string) $dateEnd
     * @param (integer) $plantCode
     * @param (string) $orderby
     * @param (string) $order
     * @param (string) $status
     * @param (bool) $standby
     * @return \Illuminate\Database\Eloquent\Collection $datasets
     */
    public static function getDatasets(
        $filter = NULL,
        $search = NULL,
        $dateStart = NULL,
        $dateEnd = NULL,
        $plantCode = NULL,
        $orderby = NULL,
        $order = NULL,
        $status = NULL,
        $standby = NULL
    )
    {
        try {
            $dateStart = Carbon::createFromFormat('d/m/Y', $dateStart)->format('Y-m-d 00:00:00') ?? NULL;
        } catch (\InvalidArgumentException $e) {
            $dateStart = NULL;
        }
        
        try {
            $dateEnd = Carbon::createFromFormat('d/m/Y', $dateEnd)->format('Y-m-d 23:59:59') ?? NULL;
        } catch (\InvalidArgumentException $e) {
            $dateEnd = NULL;
        }
        
        $locationId = NULL;
        
        if ($plantCode) {
            $locationId = Location::where('plant_code', $plantCode)->first()->id;
        }
        
        // Get Utas Codes in an array then check against it to assign is_utas property.
        $utasCodes = implode("','", UtasCode::getAllUtasCodes());
        
        // Get the shop finding piece parts count.
        //$sfPiecePartsSubSelect = self::getShopFindingPiecePartsCountQuery();
        
        // Get the notifications piece part count.
        //$notPiecePartsSubSelect = self::getNotificationPiecePartsCountQuery();
        
        $datasets = DB::table('shop_findings')
            ->select(
                'shop_findings.id',
                'shop_findings.status',
                'users.acronym',
                DB::raw("COALESCE(RCS_Segments.MPN, notifications.rcsMPN) as RCS_MPN"),
                DB::raw("COALESCE(RCS_Segments.SER, notifications.rcsSER) as RCS_SER"),
                DB::raw("COALESCE(HDR_Segments.ROC, notifications.hdrROC) as HDR_ROC"),
                DB::raw("COALESCE(HDR_Segments.RON, notifications.hdrRON) as HDR_RON"),
                DB::raw("COALESCE(RCS_Segments.MRD, notifications.rcsMRD) as RCS_MRD"),
                DB::raw("COALESCE(sf_loc.id, nt_loc.id) as loc_id"),
                
                // COALESCE(GREATEST(fieldA, fieldB),fieldA,fieldB) // Use the highest value of two fields.
                //DB::raw("COALESCE(GREATEST(sfpp.piece_part_count, npp.piece_part_count), sfpp.piece_part_count, npp.piece_part_count) as piece_part_count"),
                
                DB::raw("COALESCE(IF(RCS_Segments.MPN IN ('$utasCodes'), 1, NULL), IF(notifications.rcsMPN IN ('$utasCodes'), 1, NULL)) as is_utas"),
                'shop_findings.is_valid'
            )
            ->leftJoin('users', function($join){
                $join->on('users.planner_group', '=', 'shop_findings.planner_group')
                     ->whereNotNull('shop_findings.planner_group')
                     ->whereNotNull('users.planner_group');
            })
            ->leftJoin('locations as sf_loc', 'shop_findings.plant_code', '=', 'sf_loc.plant_code')
            ->leftJoin('HDR_Segments', 'shop_findings.id', '=', 'HDR_Segments.shop_finding_id')
            ->leftJoin('RCS_Segments', 'shop_findings.id', '=', 'RCS_Segments.SFI')
            ->leftJoin('notifications', 'shop_findings.id', '=', 'notifications.id')
            ->leftJoin('locations as nt_loc', 'notifications.plant_code', '=', 'nt_loc.plant_code')
            /*->leftJoin(DB::raw("($sfPiecePartsSubSelect) as sfpp"), function($join){
                $join->on('sfpp.shop_finding_id', '=', 'shop_findings.id');
            })
            ->leftJoin(DB::raw("($notPiecePartsSubSelect) as npp"), function($join){
                $join->on('npp.notification_id', '=', 'shop_findings.id');
            })*/
            ->whereNull('shop_findings.deleted_at')
            ->whereNotNull('shop_findings.plant_code');
            
        if ($standby) {
            $datasets = $datasets->whereNotNull('shop_findings.standby_at');
        } else {
            $datasets = $datasets->whereNull('shop_findings.standby_at');
        }
        
        if ($status) {
            $datasets = $datasets->where('shop_findings.status', $status);
        }
        
        if ($filter) {
            if ($filter == 'valid') {
                $datasets = $datasets->where('shop_findings.is_valid', 1);
            } else {
                $datasets = $datasets->where('shop_findings.is_valid', 0);
            }
        }
        
        if ($search) {
            $datasets = $datasets->whereNested(function($query) use ($search) {
                $query->where('shop_findings.id', 'LIKE', "%$search%")
                    ->orWhere('RCS_Segments.MPN', 'LIKE', "%$search%")
                    ->orWhere('notifications.rcsMPN', 'LIKE', "%$search%")
                    ->orWhere('RCS_Segments.SER', 'LIKE', "%$search%")
                    ->orWhere('notifications.rcsSER', 'LIKE', "%$search%")
                    ->orWhere('users.acronym', 'LIKE', "%$search%")
                    ->orWhere('HDR_Segments.ROC', 'LIKE', "%$search%");
            });
        }
        
        if ($dateStart) {
            $datasets = $datasets->having('RCS_MRD', '>=', $dateStart);
        }
        
        if ($dateEnd) {
            $datasets = $datasets->having('RCS_MRD', '<=', $dateEnd);
        }
        
        if ($locationId) {
            $datasets = $datasets->having('loc_id', '=', $locationId);
        }
        
        if ($orderby) {
            $datasets = $datasets->orderBy($orderby, $order);
        }
        
        $datasets = $datasets->get();
        
        return $datasets;
    }
    
    /**
     * Get a mixed set of records (notifications and shopfindings) that fit a given criteria.
     *
     * @param (string) $search
     * @param (string) $dateStart
     * @param (string) $dateEnd
     * @param (integer) $plantCode
     * @param (string) $orderby
     * @param (string) $order
     * @param (string) $status
     * @param (bool) $standby
     * @param (bool) $deleted
     * @return \Illuminate\Database\Eloquent\Collection $shopFindings
     */
    public static function getMixedDatasets(
        $search = NULL,
        $dateStart = NULL,
        $dateEnd = NULL,
        $plantCode = NULL,
        $orderby = NULL,
        $order = NULL,
        $status = NULL,
        $standby = NULL,
        $deleted = NULL
    )
    {
        try {
            $dateStart = Carbon::createFromFormat('d/m/Y', $dateStart)->format('Y-m-d 00:00:00') ?? NULL;
        } catch (\InvalidArgumentException $e) {
            $dateStart = NULL;
        }
        
        try {
            $dateEnd = Carbon::createFromFormat('d/m/Y', $dateEnd)->format('Y-m-d 23:59:59') ?? NULL;
        } catch (\InvalidArgumentException $e) {
            $dateEnd = NULL;
        }
        
        $locationId = NULL;
        
        if ($plantCode) {
            $locationId = Location::where('plant_code', $plantCode)->first()->id;
        }
        
        // Get Utas Codes in an array then check against it to assign is_utas property.
        $utasCodes = implode("','", UtasCode::getAllUtasCodes());
        
        // Get valid shopfinding ids in an array then check against it to assign is_valid property.
        $allShopFindings = ShopFinding::withTrashed()->get();
        
        $validShopFindingIdsArray = [];
        
        if (count($allShopFindings)) {
            foreach ($allShopFindings as $sf) {
                if ($sf->is_valid) {
                    $validShopFindingIdsArray[] = $sf->id;
                }
            }
        }
        
        $validShopFindingIds = implode("','", $validShopFindingIdsArray);
        
        if (!strlen($validShopFindingIds)) {
            $validShopFindingIds = '0'; // Dummy value.
        }
        
        // Get the shop finding piece parts count.
        //$sfPiecePartsSubSelect = self::getShopFindingPiecePartsCountQuery();
        
        // Get the notifications piece part count.
        //$notPiecePartsSubSelect = self::getNotificationPiecePartsCountQuery();
        
        // Get all shop finding ids even deleted ones.
        $shopFindingIds = $allShopFindings->pluck('id')->toArray();
        
        $notifications =  DB::table('notifications')
            ->select(
                'notifications.id as id',
                'notifications.status',
                'users.acronym',
                'notifications.rcsMPN as RCS_MPN',
                'notifications.rcsSER as RCS_SER',
                'notifications.hdrROC as HDR_ROC',
                'notifications.hdrRON as HDR_RON',
                'notifications.rcsMRD as RCS_MRD',
                //'npp.piece_part_count as piece_part_count',
                DB::raw("IF(notifications.rcsMPN IN ('$utasCodes'), 1, NULL) as is_utas"),
                DB::raw("IF(notifications.id IN ('$validShopFindingIds'), 1, NULL) as is_valid"),
                'locations.id as loc_id'
            )
            ->leftJoin('locations', 'notifications.plant_code', '=', 'locations.plant_code')
            ->leftJoin('users', function($join){
                $join->on('users.planner_group', '=', 'notifications.planner_group')
                     ->whereNotNull('notifications.planner_group')
                     ->whereNotNull('users.planner_group');
            })
            /*->leftJoin(DB::raw("($notPiecePartsSubSelect) as npp"), function($join){
                $join->on('npp.notification_id', '=', 'notifications.id');
            })*/
            ->leftJoin('notification_piece_parts', 'notifications.id', '=', 'notification_piece_parts.notification_id')
            
            // Remove any notifications that have a shop finding with the same id.
            ->whereNotExists(function($query){
                $query->select('id')
                    ->from('shop_findings')
                    ->whereRaw('notifications.id = shop_findings.id');
            })
            
            ->whereNotNull('notifications.plant_code')
            ->whereNull('notification_piece_parts.deleted_at');
        
        if ($status) {
            $notifications = $notifications->where('notifications.status', $status);
        }
        
        if ($standby) {
            $notifications = $notifications->whereNotNull('notifications.standby_at');
        }
        
        if ($deleted) {
            $notifications = $notifications->whereNotNull('notifications.deleted_at');
        }
        
        if ($search) {
            $notifications = $notifications->whereNested(function($query) use ($search) {
                $query->where('notifications.id', 'LIKE', "%$search%")
                    ->orWhere('notifications.rcsMPN', 'LIKE', "%$search%")
                    ->orWhere('notifications.rcsSER', 'LIKE', "%$search%")
                    ->orWhere('users.acronym', 'LIKE', "%$search%")
                    ->orWhere('notifications.hdrROC', 'LIKE', "%$search%");
            });
        }
        
        if ($dateStart) {
            $notifications = $notifications->having('RCS_MRD', '>=', $dateStart);
        }
        
        if ($dateEnd) {
            $notifications = $notifications->having('RCS_MRD', '<=', $dateEnd);
        }
        
        if ($locationId) {
            $notifications = $notifications->having('loc_id', '=', $locationId);
        }
        
        $datasets =  DB::table('shop_findings')
            ->select(
                'shop_findings.id as id',
                'shop_findings.status',
                'users.acronym',
                DB::raw("COALESCE(RCS_Segments.MPN, notifications.rcsMPN) as RCS_MPN"),
                DB::raw("COALESCE(RCS_Segments.SER, notifications.rcsSER) as RCS_SER"),
                DB::raw("COALESCE(HDR_Segments.ROC, notifications.hdrROC) as HDR_ROC"),
                DB::raw("COALESCE(HDR_Segments.RON, notifications.hdrRON) as HDR_RON"),
                DB::raw("COALESCE(RCS_Segments.MRD, notifications.rcsMRD) as RCS_MRD"),
                
                // COALESCE(GREATEST(fieldA, fieldB),fieldA,fieldB) // Use the highest value of two fields.
                //DB::raw("COALESCE(GREATEST(sfpp.piece_part_count, npp.piece_part_count), sfpp.piece_part_count, npp.piece_part_count) as piece_part_count"),
                
                DB::raw("COALESCE(IF(RCS_Segments.MPN IN ('$utasCodes'), 1, NULL), IF(notifications.rcsMPN IN ('$utasCodes'), 1, NULL)) as is_utas"),
                'shop_findings.is_valid',
                DB::raw("COALESCE(sf_loc.id, nt_loc.id) as loc_id")
            )
            ->leftJoin('users', function($join){
                $join->on('users.planner_group', '=', 'shop_findings.planner_group')
                     ->whereNotNull('shop_findings.planner_group')
                     ->whereNotNull('users.planner_group');
            })
            ->leftJoin('HDR_Segments', 'shop_findings.id', '=', 'HDR_Segments.shop_finding_id')
            ->leftJoin('locations as sf_loc', 'shop_findings.plant_code', '=', 'sf_loc.plant_code')
            ->leftJoin('RCS_Segments', 'shop_findings.id', '=', 'RCS_Segments.SFI')
            ->leftJoin('notifications', 'shop_findings.id', '=', 'notifications.id')
            ->leftJoin('locations as nt_loc', 'notifications.plant_code', '=', 'nt_loc.plant_code')
            /*->leftJoin(DB::raw("($sfPiecePartsSubSelect) as sfpp"), function($join){
                $join->on('sfpp.shop_finding_id', '=', 'shop_findings.id');
            })
            ->leftJoin(DB::raw("($notPiecePartsSubSelect) as npp"), function($join){
                $join->on('npp.notification_id', '=', 'shop_findings.id');
            })*/
            ->whereNotNull('shop_findings.plant_code');
            
        if ($status) {
            $datasets = $datasets->where('shop_findings.status', $status);
        }
        
        if ($standby) {
            $datasets = $datasets->whereNotNull('shop_findings.standby_at');
        }
        
        if ($deleted) {
            $datasets = $datasets->whereNotNull('shop_findings.deleted_at');
        }
        
        if ($search) {
            $datasets = $datasets->whereNested(function($query) use ($search) {
                $query->where('shop_findings.id', 'LIKE', "%$search%")
                    ->orWhere('RCS_Segments.MPN', 'LIKE', "%$search%")
                    ->orWhere('notifications.rcsMPN', 'LIKE', "%$search%")
                    ->orWhere('RCS_Segments.SER', 'LIKE', "%$search%")
                    ->orWhere('notifications.rcsSER', 'LIKE', "%$search%")
                    ->orWhere('users.acronym', 'LIKE', "%$search%")
                    ->orWhere('HDR_Segments.ROC', 'LIKE', "%$search%")
                    ->orWhere('notifications.hdrROC', 'LIKE', "%$search%");
            });
        }
        
        if ($dateStart) {
            $datasets = $datasets->having('RCS_MRD', '>=', $dateStart);
        }
        
        if ($dateEnd) {
            $datasets = $datasets->having('RCS_MRD', '<=', $dateEnd);
        }
        
        if ($locationId) {
            $datasets = $datasets->having('loc_id', '=', $locationId);
        }
        
        $shopFindings = $datasets->union($notifications)
            ->orderBy($orderby, $order)
            ->get();
        
        return $shopFindings;
    }
    
    /**
     * Get a filterable list of reports to export.
     *
     * @param (string) $location
     * @param (array) $status
     * @param (array) $validity
     * @param (array) $notificationIds
     * @param (array) $partNos
     * @param (date) $from
     * @param (date) $to
     * @param (string) $orderby
     * @param (string) $order
     * @return
     */
    public static function getExportList(
        $location = 'all',
        $status = ['complete_shipped', 'complete_scrapped'],
        $validity = 'all',
        $notificationIds = [],
        $partNos = [],
        $from = NULL,
        $to = NULL,
        $orderby = 'id',
        $order = 'asc'
    )
    {
        try {
            $from = Carbon::createFromFormat('d/m/Y', $from)->format('Y-m-d 00:00:00') ?? NULL;
        } catch (\InvalidArgumentException $e) {
            $from = NULL;
        }
        
        try {
            $to = Carbon::createFromFormat('d/m/Y', $to)->format('Y-m-d 23:59:59') ?? NULL;
        } catch (\InvalidArgumentException $e) {
            $to = NULL;
        }
        
        // Get Utas Codes in an array then check against it to assign is_utas property.
        $utasCodes = implode("','", UtasCode::getAllUtasCodes());
        
        // Get the shop finding piece parts count.
        //$sfPiecePartsSubSelect = self::getShopFindingPiecePartsCountQuery();
        
        // Get the notifications piece part count.
        //$notPiecePartsSubSelect = self::getNotificationPiecePartsCountQuery();
        
        $shopFindings = DB::table('shop_findings')
            ->select([
                'shop_findings.id',
                'shop_findings.status',
                'users.acronym',
                'RCS_Segments.MPN',
                'RCS_Segments.SER as RCSSER',
                'HDR_Segments.ROC',
                'HDR_Segments.RON',
                'SUS_Segments.SER as SUSSER',
                'notifications.rcsMPN',
                'notifications.rcsSER',
                'notifications.hdrROC',
                'notifications.susSER',
                DB::raw("COALESCE(shop_findings.plant_code, notifications.plant_code) as plant_code"),
                DB::raw("COALESCE(RCS_Segments.MPN, notifications.rcsMPN) as RCS_MPN"),
                DB::raw("COALESCE(RCS_Segments.SER, notifications.rcsSER) as RCS_SER"),
                DB::raw("COALESCE(HDR_Segments.ROC, notifications.hdrROC) as HDR_ROC"),
                DB::raw("COALESCE(HDR_Segments.RON, notifications.hdrRON) as HDR_RON"),
                DB::raw("COALESCE(SUS_Segments.SER, notifications.susSER) as SUS_SER"),
                //DB::raw("COALESCE(GREATEST(sfpp.piece_part_count, npp.piece_part_count), sfpp.piece_part_count, npp.piece_part_count) as piece_part_count"),
                DB::raw("COALESCE(IF(RCS_Segments.MPN IN ('$utasCodes'), 1, NULL), IF(notifications.rcsMPN IN ('$utasCodes'), 1, NULL)) as is_utas"),
                DB::raw("COALESCE(shop_findings.shipped_at, shop_findings.scrapped_at) as ship_scrap_date"),
                'shop_findings.is_valid',
                'shop_findings.scrapped_at',
                'shop_findings.shipped_at'
            ])
            ->leftJoin('users', function($join){
                $join->on('users.planner_group', '=', 'shop_findings.planner_group')
                     ->whereNotNull('shop_findings.planner_group')
                     ->whereNotNull('users.planner_group');
            })
            ->leftJoin('HDR_Segments', 'shop_findings.id', '=', 'HDR_Segments.shop_finding_id')
            ->leftJoin('RCS_Segments', 'shop_findings.id', '=', 'RCS_Segments.SFI')
            ->leftJoin('shop_findings_details', 'shop_findings.id', '=', 'shop_findings_details.shop_finding_id')
            ->leftJoin('SUS_Segments', 'SUS_Segments.shop_findings_detail_id', '=', 'shop_findings_details.id')
            ->leftJoin('notifications', 'shop_findings.id', '=', 'notifications.id')
            /*->leftJoin(DB::raw("($sfPiecePartsSubSelect) as sfpp"), function($join){
                $join->on('sfpp.shop_finding_id', '=', 'shop_findings.id');
            })
            ->leftJoin(DB::raw("($notPiecePartsSubSelect) as npp"), function($join){
                $join->on('npp.notification_id', '=', 'shop_findings.id');
            })*/
            ->whereNull('shop_findings.deleted_at')
            ->whereNull('shop_findings.standby_at')
            ->whereNotNull('shop_findings.plant_code');
            
        if ($location != 'all') {
            $shopFindings = $shopFindings->having('plant_code', $location);
        }
        
        if ($status) {
            foreach ($status as $k => $v) {
                if (array_key_exists($v, self::$statuses)) {
                    $statuses[] = $v;
                }
            }
            
            $shopFindings = $shopFindings->whereIn('shop_findings.status', $statuses);
        }
        
        if ($validity != 'all') {
            $validity = $validity == 'valid' ? 'valid' : 'invalid';
            
            if ($validity == 'valid') {
                $shopFindings = $shopFindings->where('shop_findings.is_valid', 1);
            } else {
                $shopFindings = $shopFindings->where('shop_findings.is_valid', 0);
            }
        }
        
        if (!empty($notificationIds)) {
            $regexp = implode('|', $notificationIds);
            $shopFindings = $shopFindings->where('shop_findings.id', 'regexp', $regexp);
        }
        
        if (!empty($partNos)) {
            $regexp = implode('|', $partNos);
            $shopFindings = $shopFindings->having('RCS_MPN', 'regexp', $regexp);
        }
        
        if ($from) {
            $shopFindings = $shopFindings->whereNested(function($q) use ($from) {
                $q->where('shop_findings.shipped_at', '>=', $from)->orWhere('shop_findings.scrapped_at', '>=', $from);
            });
        }
        
        if ($to) {
            $shopFindings = $shopFindings->whereNested(function($q) use ($to) {
                $q->where('shop_findings.shipped_at', '<=', $to)->orWhere('shop_findings.scrapped_at', '<=', $to);
            });
        }
        
        $shopFindings = $shopFindings->orderBy($orderby, $order)->get();
        
        return $shopFindings;
    }
    
    /**
     * Get the author id.
     *
     * @return string
     */
    public function get_ATA_AuthorId()
    {
        return (string) $this->ataID;
    }
    
    /**
     * Get the author version.
     *
     * @return integer
     */
    public function get_ATA_AuthorVersion()
    {
        return (integer) $this->ataVersion;
    }
    
    /**
     * Get the shop findings version.
     *
     * @return integer
     */
    public function get_SF_Version()
    {
        return (integer) $this->SFVersion;
    }
}
