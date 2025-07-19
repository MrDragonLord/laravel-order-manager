<?php

namespace App\Http\Controllers;

use App\Http\Resources\StockResource;
use App\Models\Stock;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StockController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $stocks = Stock::with('product')->get();
        return StockResource::collection($stocks);
    }
}
