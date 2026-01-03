<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\QuoteRequestReceived;
use App\Mail\QuoteRequestNotification;

class QuoteController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'project_type' => 'required|string|in:windows,doors,curtains,railings,pergola,kitchen,shelter,shutters,facades,veranda,other',
            'description' => 'required|string|max:2000',
            'budget_range' => 'nullable|string|max:100',
            'timeline' => 'nullable|string|max:100',
        ]);

        $quote = Quote::create($validated);

        // Send notification emails (queued)
        try {
            Mail::to($quote->email)->queue(new QuoteRequestReceived($quote));
            Mail::to(config('mail.admin_email', 'admin@aluminiumcraft.tn'))->queue(new QuoteRequestNotification($quote));
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Log::error('Failed to send quote emails: ' . $e->getMessage());
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.quote_success'),
            ]);
        }

        return back()->with('success', __('messages.quote_success'));
    }
}
