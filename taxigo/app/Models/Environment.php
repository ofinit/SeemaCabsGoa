<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Environment extends Model
{
    use SoftDeletes;

    protected $fillable = ['title','value'];

    public function getSplashLogoImageAttribute(){
        if($this->title==='splashscreenlogo'){
            return $this->logoUrl().'/'.$this->value;
        }
    }

    public function getAppLogoImageAttribute(){
        if($this->title==='applogo'){
            return $this->logoUrl().'/'.$this->value;
        }
    }

    public function getDriverSplashLogoImageAttribute(){
        if($this->title==='driversplashlogo'){
            return $this->logoUrl().'/'.$this->value;
        }
    }

    public function getDriverAppLogoImageAttribute(){
        if($this->title==='driverapplogo'){
            return $this->logoUrl().'/'.$this->value;
        }
    }

    public function getAdminPanelLogoImageAttribute(){
        if($this->title==='adminpanellogo'){
            return $this->logoUrl().'/'.$this->value;
        }
    }

    public function getInvoiceLogoImageAttribute(){
        if($this->title==='invoicelogo'){
            return $this->logoUrl().'/'.$this->value;
        }
    }

    public function getMailLogoImageAttribute(){
        if($this->title==='maillogo'){
            return $this->logoUrl().'/'.$this->value;
        }
    }

    public function scopeLogoUrl(Builder $query)
    {
        return asset('storage/logo/');
    }

    public static function getValue(string $key, $default = null)
    {
        return optional(static::where('title', $key)->first())->value ?? $default;
    }



}
