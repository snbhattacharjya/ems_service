<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssemblyParty extends Model
{
    //
    protected $table = 'assembly_party';
    public $incrementing = false;

    public function AssemblyParty(){
        return $this->belongsTo('App\AssemblyConstituency');
    }
}
