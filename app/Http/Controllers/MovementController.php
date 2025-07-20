<?php

namespace App\Http\Controllers;

use App\Http\Resources\MovementResource;
use App\Models\Movement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MovementController extends Controller
{
    /**
     * History movement
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $limit = (int) $request->query('limit', 15);

        $paginator = Movement::with(['warehouse', 'product'])
            ->scopeWarehouse($request->query('warehouse_id'))
            ->scopeProduct($request->query('product_id'))
            ->dateFrom($request->query('date_from'))
            ->dateTo($request->query('date_to'))
            ->paginate($limit);

        $data = MovementResource::collection($paginator->items());

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
