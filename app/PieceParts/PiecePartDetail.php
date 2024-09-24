<?php

namespace App\PieceParts;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PiecePartDetail extends Model
{
    use SoftDeletes;
    
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
    
    protected $dates = ['deleted_at'];
    
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
    protected $fillable = ['id', 'piece_part_id', 'deleted_at'];
    
    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['PiecePart'];
    
    /**
     * Get the piece part associated with the piece part detail record.
     */
    public function PiecePart()
    {
        return $this->belongsTo('App\PieceParts\PiecePart');
    }
    
    /**
     * Get the next higher assembly associated with the piece part record.
     */
    public function NHS_Segment()
    {
        return $this->hasOne('App\PieceParts\NHS_Segment');
    }
    
    /**
     * Get the next worked piece part associated with the piece part record.
     */
    public function WPS_Segment()
    {
        return $this->hasOne('App\PieceParts\WPS_Segment');
    }
    
    /**
     * Get the next replaced piece part associated with the piece part record.
     */
    public function RPS_Segment()
    {
        return $this->hasOne('App\PieceParts\RPS_Segment');
    }
}
