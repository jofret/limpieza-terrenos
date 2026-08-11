<?php

namespace App\Http\Controllers;

use App\Models\ServiceOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PublicBudgetController extends Controller
{
    public function show(string $token)
    {
        $serviceOrder = ServiceOrder::with(['customer', 'property', 'category'])
            ->where('budget_token', $token)
            ->firstOrFail();

        return view('budget.show', ['serviceOrder' => $serviceOrder]);
    }

    public function accept(Request $request, string $token): RedirectResponse|JsonResponse
    {
        $serviceOrder = ServiceOrder::where('budget_token', $token)->firstOrFail();

        $serviceOrder->acceptBudget($request->input('payment_method_preference', []));

        if ($request->wantsJson()) {
            return response()->json(['status' => 'ok']);
        }

        return redirect()->route('budget.show', $token);
    }
}
