<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GreenProspectProgress extends Model
{
    //
    protected $table = 'green_prospect_progresses';

    protected $primaryKey = 'progress_id';

    public function client() {
        return $this->belongsTo('App\GreenProspectClient', 'green_id', 'green_id');
    }

    public function createdBy() {
        return $this->belongsTo('App\User', 'created_by');
    }

    public function updatedBy() {
        return $this->belongsTo('App\User', 'updated_by');
    }
}
