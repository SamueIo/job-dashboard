<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Email;

use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {

        $user = Auth::user();

        $upcomingInterviews = Email::where('user_id', $user->id)
            ->where('status', 'interview')
            ->latest()
            ->take(5)
            ->get();

        $recentActivity = Email::where('user_id', $user->id)
            ->whereIn('status', [
                'interview',
                'rejected',
                'offer',
                'applied'
            ])
            ->latest()
            ->take(10)
            ->get();
        

        // Data for StratsGrid
        $total = Email::where('user_id', $user->id)->count();

        if($total !==0){

            $applications = Email::where('user_id', $user->id)
                ->where('status', 'applied')
                ->count();
    
            $interviews = Email::where('user_id', $user->id)
                ->where('status', 'interview')
                ->count();
    
            $responses = Email::where('user_id', $user->id)
                ->whereIn('status', ['interview', 'rejected', 'offer'])
                ->count();
    
            $positive = Email::where('user_id', $user->id)
                ->whereIn('status', ['interview', 'offer'])
                ->count();
            
            $rejections = Email::where('user_id', $user->id)
                ->where('status', 'rejected')
                ->count();
            
            $other = Email::where('user_id', $user->id)
                ->where('status', 'other')
                ->count();

            $socialRate = $applications > 0
                ? round(($positive / $applications) * 100, 1)
                : 0;
        }else{
            $applications = 0;
            $interviews = 0;
            $responses = 0;
            $positive = 0;
            $socialRate = 0;
            $rejections= 0;
            $other= 0;
        }
        
        return Inertia::render('Dashboard', [
            'upcomingInterviews' => $upcomingInterviews,
            'recentActivity' => $recentActivity,
            'stats' => [
            'applications' => $applications,
                'interviews' => $interviews,
                'responses' => $responses,
                'social_rate' => $socialRate,
                'rejections' => $rejections,
                'positive' => $positive,
                'other' => $other,
            ]
        ]);
    }
}
