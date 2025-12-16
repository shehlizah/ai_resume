<x-layouts.app :title="__('Job Applications')">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">Job Applications</h1>
            @if(isset($job))
                <p class="text-muted mb-0">Applicants for <strong>{{ $job->title }}</strong></p>
            @else
                <p class="text-muted mb-0">All applicants across your posted jobs.</p>
            @endif
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('company.jobs.index') }}" class="btn btn-light">My Jobs</a>
            <a href="{{ route('company.dashboard') }}" class="btn btn-primary">Post a Job</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Applications</h5>
        </div>
        <div class="card-body">
            @if($applications->isEmpty())
                <p class="text-muted mb-0">No applications yet.</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Applicant</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Job</th>
                                <th>Resume</th>
                                <th>Applied</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($applications as $application)
                                <tr>
                                    <td>{{ $application->applicant_name }}</td>
                                    <td>{{ $application->applicant_email }}</td>
                                    <td>{{ $application->applicant_phone ?? '-' }}</td>
                                    <td>{{ optional($application->job)->title }}</td>
                                    <td>
                                        @if($application->resume_url)
                                            <a href="{{ $application->resume_url }}" target="_blank" rel="noopener">View</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ optional($application->created_at)->format('Y-m-d') }}</td>
                                </tr>
                                @if($application->cover_letter)
                                    <tr>
                                        <td colspan="6" class="bg-light">
                                            <strong>Cover Letter:</strong>
                                            <div class="text-muted">{{ $application->cover_letter }}</div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">{{ $applications->links() }}</div>
            @endif
        </div>
    </div>
</x-layouts.app>
