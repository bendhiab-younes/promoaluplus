<?php

namespace App\Http\Controllers;

use App\Mail\QuoteRequestNotification;
use App\Mail\QuoteRequestReceived;
use App\Models\Quote;
use App\Support\CanonicalServiceCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class QuoteController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'project_types' => 'required|array|min:1',
            'project_types.*' => CanonicalServiceCatalog::quoteItemValidationRule(),
            'description' => 'required|string|max:2000',
            'timeline' => 'nullable|string|max:100',
        ]);

        $quote = Quote::create($validated);

        // Send notification emails (queued)
        try {
            Mail::to($quote->email)->queue(new QuoteRequestReceived($quote));
            Mail::to(config('mail.admin_email'))->queue(new QuoteRequestNotification($quote));
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Log::error('Failed to send quote emails: '.$e->getMessage());
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
