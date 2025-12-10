<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $jobs = Job::active()
            ->recent()
            ->paginate(7);

        return response()->json([
            'jobs' => $jobs,
            'total' => Job::active()->count()
        ]);
    }

    public function show(Job $job)
    {
        return response()->json($job);
    }
}
