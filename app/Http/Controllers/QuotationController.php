<?php

namespace App\Http\Controllers;

use App\Enums\Currency;
use App\Http\Requests\StoreQuotationRequest;
use App\Http\Resources\QuotationResource;
use App\Services\QuotationCalculator;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class QuotationController extends Controller
{
    public function index(): View
    {
        return view('quotation');
    }

    public function store(StoreQuotationRequest $request, QuotationCalculator $quotationCalculator): JsonResponse
    {
        $calculation = $quotationCalculator->calculate(
            $request->ages(),
            CarbonImmutable::parse($request->validated('start_date')),
            CarbonImmutable::parse($request->validated('end_date')),
        );

        $quotation = $request->user()->quotations()->create([
            'ages' => $request->ages(),
            'currency_id' => Currency::from($request->validated('currency_id')),
            'start_date' => $request->validated('start_date'),
            'end_date' => $request->validated('end_date'),
            'trip_length' => $calculation['tripLength'],
            'total' => $calculation['total'],
        ]);

        return (new QuotationResource($quotation))
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }
}
