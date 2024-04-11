<?php

namespace App\Http\Controllers\Api;

use App\DTO\Supports\CreateSupportDTO;
use App\DTO\Supports\UpdateSupportDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdateSupportRequest;
use App\Http\Resources\SupportResource;
use App\Services\SupportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SupportController extends Controller
{

    public function __construct(protected SupportService $service)
    {
        
    }
    public function index(Request $request)
    {
        $supports = $this->service->paginate(
            page:$request->get('page', 1),
            totalPerPage:$request->get('per_page',1),
            filter:$request->filter
        );

        return SupportResource::collection($supports->items())
        ->additional([
            'meta' => [
                'total' => $supports->total(),
                'is_first_page' => $supports->isFirstPage(),
                'is_last_page' => $supports->isLastPage(),
                'current_page' => $supports->currentPage(),
                'next_page' => $supports->getNumberNextPage(),
                'previous_page' => $supports->getNumberPreviousPage(),
            ]
        ]);
    }

    public function store(StoreUpdateSupportRequest $request)
    {
        $support = $this->service->new(
            CreateSupportDTO::makeFromRequest($request)
        );

        return new SupportResource($support);
    }

    public function show(string $id)
    {
        if (!$support = $this->service->findOne($id)) {
            return response()->json([
                'error' => 'Not Found'
            ], 404);
        }

        return new SupportResource($support);
    }

    public function update(StoreUpdateSupportRequest $request, string $id)
    {
        $support = $this->service->update(
            UpdateSupportDTO::makeFromRequest($request, $id)
        );

        if (!$support) {
            return response()->json([
                'error' => 'Not Found'
            ], Response::HTTP_NOT_FOUND);
        }
        return new SupportResource($support);
    }

    public function destroy(string $id)
    {
        if (!$this->service->findOne($id)) {
            return response()->json([
                'error' => 'Not Found'
            ], 404);
        }
        $this->service->delete($id);
        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
