<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssemblyConstituency extends Model
{
    protected $table = 'assembly_constituencies';
    public $incrementing = false;

    public function assemblyParty()
    {
        return $this->hasOne('App\AssemblyParty','id','assembly_id');
    }
}
