<?php

namespace Davidhsianturi\Compass\Storage;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Davidhsianturi\Compass\Compass;
use Davidhsianturi\Compass\RouteResult;
use Davidhsianturi\Compass\Contracts\RequestRepository;
use Illuminate\Support\Facades\Auth;

class DatabaseRequestRepository implements RequestRepository
{
    /**
     * The database connection name that should be used.
     *
     * @var string
     */
    protected $connection;

    /**
     * Create a new database repository.
     *
     * @param  string  $connection
     * @return void
     */
    public function __construct(string $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Return all the route requests.
     *
     * @return \Illuminate\Support\Collection|Davidhsianturi\Compass\RouteResult[]
     */
    public function get()
    {
        return Compass::syncRoute($this->routesInStorage())->map(function ($route) {
            return $this->routeResult($route, []);
        });
    }

    /**
     * find the route with the given ID.
     *
     * @param  string  $id
     * @return \Davidhsianturi\Compass\RouteResult
     */
    public function find(string $id): RouteResult
    {
        $route = Compass::syncRoute($this->routesInStorage())
            ->whereStrict('route_hash', $id)
            ->first();

        $responses = $this->table('compass_routeables')
            ->whereExample(true)
            ->where('route_hash', $id)
            ->get()
            ->toArray();

        return $this->routeResult($route, $responses);
    }

    /**
     * find the route with the given ID.
     *
     * @param  string  $id
     * @return \Davidhsianturi\Compass\RouteResult
     */
    public function findEmpty(string $id): RouteResult
    {
        $uuid = (string) Str::uuid();
        $methods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'];
        return new RouteResult(
            md5($uuid.':'.implode($methods)),
            $uuid,
            '',
            $id,
            null,
            [],
            [
                'domain' => '',
                'methods' => $methods,
                'uri' => '',
                'name' => '',
                'action' => '',
            ],
            Carbon::now(),
            Carbon::now(),
            false,
            ''
        );
    }

    /**
     * Update or create the given route.
     *
     * @param  array  $route
     * @return \Davidhsianturi\Compass\RouteResult
     */
    public function save(array $route, $collection = false)
    {
        $storageId = $route['storageId'] ?? (string) Str::uuid();

        $store = RouteModel::on($this->connection)->updateOrCreate(
            ['route_hash' => $route['id'], 'uuid' => $storageId],
            [
                'title' => $route['title'],
                'description' => $route['description'],
                'content' => $route['content'],
            ]
        );

        if($collection) {
            $collection = RouteCollectionModel::where('name', $collection)->first();
            $collectionComposite = new CollectionCompositeModel();
            $collectionComposite->collection_id = $collection->id;
            $collectionComposite->route_id = $storageId;
            $collectionComposite->save();
        }

        $syncedRoute = Compass::syncRoute($store->get()->toArray())
            ->whereStrict('uuid', $store->uuid)
            ->first();

        return $this->routeResult($syncedRoute, []);
    }

    /**
     * The route result.
     *
     * @param  array|null  $route
     * @param  array|null  $responses
     * @return \Davidhsianturi\Compass\RouteResult
     */
    protected function routeResult(?array $route, ?array $responses)
    {
        if (blank($route)) {
            return;
        }

        return new RouteResult(
            $route['route_hash'],
            $route['uuid'],
            $route['title'],
            isset($route['name']) ? $route['name'] : null,
            $route['description'],
            $route['content'],
            [
                'domain' => isset($route['domain']) ? $route['domain'] : '',
                'methods' => isset($route['methods']) ? $route['methods'] : '',
                'uri' => isset($route['uri']) ? $route['uri'] : '',
                'name' => isset($route['name']) ? $route['name'] : '',
                'action' => isset($route['action']) ? $route['action'] : '',
            ],
            Carbon::parse($route['created_at']),
            Carbon::parse($route['updated_at']),
            $route['example'],
            $responses
        );
    }

    /**
     * Get routes from storage.
     *
     * @return array
     */
    protected function routesInStorage()
    {
        if(\App::runningInConsole() || \Auth::user()->isAdmin()){
            return RouteModel::on($this->connection)
                ->select(
                          'compass_routeables.*',
                          'routeable_collections.*'
                          )
                ->join('collections_composite', 'compass_routeables.uuid', '=', 'collections_composite.route_id')
                ->join('routeable_collections', 'collections_composite.collection_id', '=', 'routeable_collections.id')
                ->whereExample(false)
                ->get()
                ->toArray();
        } else {
            return RouteModel::on($this->connection)
                ->whereExample(false)
                ->join('collections_composite', 'compass_routeables.uuid', '=', 'collections_composite.route_id')
                ->join('routeable_collections', 'collections_composite.collection_id', '=', 'routeable_collections.id')
                ->join('user_routes_composite', 'routeable_collections.id', '=', 'user_routes_composite.collection_id')
                ->where('user_routes_composite.user_id', '=', \Auth::user()->id)
                ->get()
                ->toArray();
        }
    }

    /**
     * Get routes from storage.
     *
     * @return array
     */
    public function routesInStorageCollect()
    {
        return collect(RouteModel::on($this->connection)
            ->whereExample(false)
            ->get()
            ->toArray());
    }

    /**
     * Get a query builder instance for the given table.
     *
     * @param  string  $table
     * @return \Illuminate\Database\Query\Builder
     */
    protected function table($table)
    {
        return DB::connection($this->connection)->table($table);
    }
}
