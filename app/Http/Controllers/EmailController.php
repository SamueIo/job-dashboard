<?php

namespace App\Http\Controllers;

use App\Enums\EmailStatus;
use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EmailController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $user = Auth::user();

        $emails = $this->buildQuery($request, $user)
            ->paginate(10);

        $statusCounts = Email::query()
            ->where('user_id', $user->id)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $filters = [
            'search' => $request->search ?? '',
            'status' => $request->status ?? 'all',
            'company' => $request->company ?? '',
            'sort' => $request->sort ?? 'newest',
        ];

        return Inertia::render('Emails', [
            'emails' => $emails,

            'processingCount' => Email::query()
                ->where('user_id', $user->id)
                ->whereNull('status')
                ->count(),

            'statusCounts' => $statusCounts,

            'statuses' => collect(EmailStatus::cases())
                ->map(fn ($status) => $status->value)
                ->values(),

            'companies' => Email::query()
                ->where('user_id', $user->id)
                ->whereNotNull('company')
                ->distinct()
                ->orderBy('company')
                ->pluck('company')
                ->values(),

            'filters' => $filters,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER API
    |--------------------------------------------------------------------------
    */

    public function filter(Request $request)
    {
        $user = Auth::user();

        $emails = $this->buildQuery($request, $user)
            ->paginate(10);

        return response()->json($emails);
    }

    /*
    |--------------------------------------------------------------------------
    | QUERY BUILDER
    |--------------------------------------------------------------------------
    */

    private function buildQuery(Request $request, $user)
    {
        $query = Email::query()
            ->where('user_id', $user->id);

        /*
        |----------------------------------------------------------------------
        | SEARCH
        |----------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('from', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%")
                    ->orWhere('snippet', 'like', "%{$search}%")
                    ->orWhere('summary', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%");
            });
        }
          /*
        |--------------------------------------------------------------------------
        | STATUS COUNTS
        |--------------------------------------------------------------------------
        */

        $statusCounts = (clone $query)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        /*
        |----------------------------------------------------------------------
        | STATUS
        |----------------------------------------------------------------------
        */

        if (
            $request->filled('status') &&
            $request->status !== 'all'
        ) {

            $query->where(
                'status',
                $request->status
            );
        }

        /*
        |----------------------------------------------------------------------
        | COMPANY
        |----------------------------------------------------------------------
        */

        if ($request->filled('company')) {

            $query->where(
                'company',
                $request->company
            );
        }

        /*
        |----------------------------------------------------------------------
        | SORT
        |----------------------------------------------------------------------
        */

        switch ($request->sort) {

            case 'oldest':

                $query->orderBy('gmail_date', 'asc');

                break;

            case 'confidence':

                $query->orderByDesc('confidence');

                break;

            case 'priority':

                $query->orderByRaw("
                    CASE priority
                        WHEN 'high' THEN 1
                        WHEN 'medium' THEN 2
                        WHEN 'low' THEN 3
                        ELSE 4
                    END ASC
                ");

                break;

            default:

                $query->orderBy('gmail_date', 'desc');

                break;
        }

        /*
        |----------------------------------------------------------------------
        | UNREAD FIRST
        |----------------------------------------------------------------------
        */

        $query->orderBy('seen');

        return $query;
    }

    public function markAsSeen(Email $email)
    {
        $email->update([
            'seen' => true,
        ]);
        return response()->json([
            'success' => true,
        ]);
    }

    
}