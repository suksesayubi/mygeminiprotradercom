<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpertSignal;
use App\Models\UserNotification;

class ExpertSignalController extends Controller
{
    public function index(Request $request)
    {
        $query = ExpertSignal::with(['creator', 'approver'])
            ->where('status', 'published')
            ->latest('published_at');

        // Apply filters
        if ($request->filled('pair')) {
            $query->where('pair', $request->pair);
        }

        if ($request->filled('signal_type')) {
            $query->where('signal_type', $request->signal_type);
        }

        $signals = $query->paginate(20);

        // Get unique pairs for filter
        $pairs = ExpertSignal::where('status', 'published')
            ->distinct()
            ->pluck('pair')
            ->filter()
            ->sort()
            ->values();

        return view('expert-signals.index', compact('signals', 'pairs'));
    }

    public function show(ExpertSignal $expertSignal)
    {
        // Only show published signals to regular users
        if ($expertSignal->status !== 'published') {
            abort(404);
        }

        return view('expert-signals.show', compact('expertSignal'));
    }
}
