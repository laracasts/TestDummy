<?php namespace Acme;

class User extends \Illuminate\Database\Eloquent\Model
{
    public function role()
    {
        return $this->belongsTo('Acme\Role');
    }
}
