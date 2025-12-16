<x-layouts.app :title="__('My Jobs')">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">My Jobs</h1>
            <p class="text-muted mb-0">Manage your posted jobs and view applicants.</p>
        </div>
        <a href="{{ route('company.dashboard') }}" class="btn btn-primary">Post a Job</a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Posted Jobs</h5>
        </div>
        <div class="card-body">
            @if($jobs->isEmpty())
                <p class="text-muted mb-0">No jobs posted yet.</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Location</th>
                                <th>Type</th>
                                <th>Featured</th>
                                <th>Applicants</th>
                                <th>Posted</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jobs as $job)
                                <tr>
                                    <td>{{ $job->title }}</td>
                                    <td>{{ $job->location }}</td>
                                    <td>{{ $job->type }}</td>
                                    <td>{{ $job->is_featured ? 'Yes' : 'No' }}</td>
                                    <td>{{ $job->applications_count }}</td>
                                    <td>{{ optional($job->created_at)->format('Y-m-d') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('company.jobs.applications', $job) }}" class="btn btn-sm btn-outline-primary">View Applicants</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">{{ $jobs->links() }}</div>
            @endif
        </div>
    </div>
</x-layouts.app>
