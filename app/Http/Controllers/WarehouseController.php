<?php

namespace App\Http\Controllers;

use App\Http\Resources\WarehouseResource;
use App\Http\Resources\WarehouseStockResource;
use App\Models\Warehouse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WarehouseController extends Controller
{
    /**
     * Все склады
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return WarehouseResource::collection(Warehouse::all());
    }

    /**
     * Cписок товаров с их остатками по складам
     *
     * @return AnonymousResourceCollection
     */
    public function productsByWarehouse(): AnonymousResourceCollection
    {
        $warehouses = Warehouse::with('stocks.product')->get();

        return WarehouseStockResource::collection($warehouses);
    }
}
