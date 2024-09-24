<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class SAPAccessToken extends Model
{
    protected $table = 'sap_access_tokens';
    
    /**
     * Has the token expired.
     *
     * @return boolean
     */
    public function hasExpired()
    {
        return $this->expires_at <= Carbon::now();
    }
    
    /**
     * Get the access token.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
    
    /**
     * Get the token scope.
     *
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }
    
    /**
     * Get the token.
     *
     * @param  string  $value
     * @return string
     */
    public function getTokenAttribute($value)
    {
        return Crypt::decryptString($value);
    }
    
    /**
     * Set the token attribute.
     *
     * @param  string  $value
     * @return void
     */
    public function setTokenAttribute($value)
    {
        $this->attributes['token'] = Crypt::encryptString($value);
    }
    
    /**
     * Set the expires_at attribute.
     *
     * @param  string  $value
     * @return void
     */
    public function setExpiresAtAttribute($value)
    {
        $this->attributes['expires_at'] = Carbon::now()->addSeconds($value);
    }
}
