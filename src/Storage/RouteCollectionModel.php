<?php

namespace Davidhsianturi\Compass\Storage;

use Illuminate\Database\Eloquent\Model;

class RouteCollectionModel extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'routeable_collections';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';


    public function routes()
    {
        return $this->belongsToMany('Davidhsianturi\Compass\Storage\RouteModel', 'collections_composite', 'collection_id', 'route_id');
    }

    public function user()
    {
        return $this->belongsToMany('\App\Models\User', 'user_routes_composite', 'collection_id', 'user_id');
    }
}
