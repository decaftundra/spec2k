<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    /**
     |-----------------------------
     | Dynamic Local Scopes
     |-----------------------------
     */
    
    /**
     * Scope the query to only include icao codes like a given string.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string $OPR
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIcao($query, $OPR = NULL)
    {
        if (!$OPR) return $query;
        return $query->where('icao', 'LIKE', "%$OPR%");
    }
    
    /**
     * Scope the query to only include company names like a given string.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string $WHO
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompanyName($query, $WHO = NULL)
    {
        if (!$WHO) return $query;
        return $query->where('company_name', 'LIKE', "%$WHO%");
    }
    
    /**
     * Scope the query to only include icao codes like a given string.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string $OPR
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIcaoExact($query, $OPR = NULL)
    {
        if (!$OPR) return $query;
        return $query->where('icao', $OPR);
    }
    
    /**
     * Scope the query to only include company names like a given string.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string $WHO
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompanyNameExact($query, $WHO = NULL)
    {
        if (!$WHO) return $query;
        return $query->where('company_name', $WHO);
    }
    
    /**
     * Filter the results.
     *
     * @param (string) $OPR
     * @param (string) $WHO
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function getCustomers($OPR = NULL, $WHO = NULL)
    {
        return static::icao($OPR)->companyName($WHO)->get();
    }
    
    /**
     * Filter the results.
     *
     * @param (string) $OPR
     * @param (string) $WHO
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function getCustomer($OPR = NULL, $WHO = NULL)
    {
        return static::icaoExact($OPR)->companyNameExact($WHO)->get();
    }
    
    /**
     * Search customers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search = NULL)
    {
        if (!$search) return $query;
        return $query->where('company_name', 'LIKE', "%$search%")
            ->orWhere('icao', 'LIKE', "%$search%");
    }
}
