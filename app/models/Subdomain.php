<?php 

use Illuminate\Database\Eloquent\Model as Eloquent;
 
class Subdomain extends Eloquent {
 
    protected $table = 'subdomains';
    protected $softDelete = true;
 
    public function owner()
    {
        return $this->belongsTo('App\Models\User', 'user_id')->withTimestamps();
    }
    
    protected $guarded = [
	    "id",
	    "created_at",
	    "updated_at",
	    "deleted_at"
    ];
}