@section('title', __('Cover Letters'))
<x-layouts.app :title="__('Cover Letters')">
    <div class="row g-4 mb-4">
        <!-- Header -->
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-1">📝 Cover Letters</h4>
                    <p class="text-muted mb-0">Create, manage, and customize your cover letters</p>
                </div>
                <a href="{{ route('user.cover-letters.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus-circle me-1"></i> New Cover Letter
                </a>
            </div>
        </div>

        <!-- Empty State -->
        @if($coverLetters->isEmpty())
        <div class="col-lg-12">
            <div class="border rounded-3 p-5 text-center">
                <div style="font-size: 3rem; margin-bottom: 1rem;">📄</div>
                <h5 class="mb-2">No Cover Letters Yet</h5>
                <p class="text-muted mb-3">Create your first cover letter to start applying for jobs</p>
                <a href="{{ route('user.cover-letters.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus-circle me-1"></i> Create Cover Letter
                </a>
            </div>
        </div>
        @else
        <!-- Cover Letters Table -->
        <div class="col-lg-12">
            <div class="border rounded">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3">Title</th>
                                <th>Company</th>
                                <th>Recipient</th>
                                <th>Created</th>
                                <th class="text-end px-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($coverLetters as $letter)
                            <tr>
                                <td class="px-3">
                                    <div class="fw-semibold">{{ $letter->title }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-light-primary">{{ $letter->company_name }}</span>
                                </td>
                                <td>{{ $letter->recipient_name }}</td>
                                <td>
                                    <small class="text-muted">{{ $letter->created_at->format('M d, Y') }}</small>
                                </td>
                                <td class="text-end px-3">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('user.cover-letters.view', $letter->id) }}" class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="bx bx-eye"></i>
                                        </a>
                                        <a href="{{ route('user.cover-letters.edit', $letter->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="bx bx-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $letter->id }}" title="Delete">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if($coverLetters->hasPages())
        <div class="col-lg-12">
            <div class="d-flex justify-content-center">
                {{ $coverLetters->links() }}
            </div>
        </div>
        @endif
        @endif
    </div>

    <!-- Delete Modals -->
    @foreach($coverLetters as $letter)
    <div class="modal fade" id="deleteModal{{ $letter->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Cover Letter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong>{{ $letter->title }}</strong>? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('user.cover-letters.destroy', $letter->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</x-layouts.app>
