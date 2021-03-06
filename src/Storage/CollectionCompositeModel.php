<?php

namespace Davidhsianturi\Compass\Storage;

use Illuminate\Database\Eloquent\Model;

class CollectionCompositeModel extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'collections_composite';

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


    public function route()
    {
        return $this->belongsTo('Davidhsianturi\Compass\Storage\RouteModel', 'route_id');
    }

    public function collection()
    {
        return $this->belongsTo('Davidhsianturi\Compass\Storage\RouteCollectionModel', 'collection_id');
    }
}
