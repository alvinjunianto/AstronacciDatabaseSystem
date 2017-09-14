<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AclubMember extends Model
{
    //
    protected $table = 'aclub_members';

    public function master() {
        return $this->belongsTo('App\MasterClient', 'master_id', 'master_id');
    }

    public function aclub_transactions() {
        return $this->hasMany('App\AclubTransaction', 'master_id', 'master_id');
    }

    public function createdby() {
        return $this->belongsTo('App\User', 'created_by');
    }

    public function updatedby() {
        return $this->belongsTo('App\User', 'updated_by');
    }
}
