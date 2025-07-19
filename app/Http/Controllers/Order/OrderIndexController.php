<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderIndexController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $limit = (int) $request->query('limit', 15);

        $paginator = Order::with(['warehouse', 'items', 'items.product', 'items.product.stock'])
            ->status($request->query('status'))
            ->customer($request->query('customer'))
            ->dateFrom($request->query('date_from'))
            ->dateTo($request->query('date_to'))
            ->paginate($limit);

        $data = OrderResource::collection($paginator->items());

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'limit'        => $limit,
                'total'        => $paginator->total(),
            ],
        ]);
    }
}
