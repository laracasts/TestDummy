<?php namespace Acme;

class Role extends \Illuminate\Database\Eloquent\Model
{
    public function users()
    {
        return $this->hasMany('Acme\User');
    }
}
