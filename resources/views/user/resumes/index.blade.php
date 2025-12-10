<x-layouts.app :title="'My Resumes'">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">📄 My Resumes ({{ $resumes->total() }})</h5>
          <a href="{{ route('user.resumes.choose') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Create New Resume
          </a>
        </div>

        <div class="card-body">
          @php
            $hasAnyResumes = \App\Models\UserResume::where('user_id', Auth::id())->exists();
          @endphp

          @if($hasAnyResumes)
          <!-- Filter and Sort Controls -->
          <div class="row g-3 mb-4">
            <div class="col-md-4">
              <label class="form-label small text-muted">Search</label>
              <input type="text"
                     class="form-control"
                     id="searchInput"
                     placeholder="Search by name or title..."
                     value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
              <label class="form-label small text-muted">Status</label>
              <select class="form-select" id="statusFilter" onchange="applyFilters()">
                <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>All Status</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label small text-muted">Sort By</label>
              <select class="form-select" id="sortFilter" onchange="applyFilters()">
                <option value="latest" {{ request('sort_by') == 'latest' || !request('sort_by') ? 'selected' : '' }}>Latest First</option>
                <option value="oldest" {{ request('sort_by') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name (A-Z)</option>
              </select>
            </div>
          </div>
          @endif

          @if($resumes->isEmpty())
            <div class="text-center py-5">
              <i class="bx bx-file" style="font-size: 64px; color: #ddd;"></i>
              @if($hasAnyResumes && (request('search') || request('status')))
                <h5 class="mt-3 text-muted">No resumes match your filters</h5>
                <p class="text-muted">Try adjusting your search or filters</p>
                <button onclick="clearFilters()" class="btn btn-outline-primary mt-2">
                  <i class="bx bx-x me-1"></i> Clear Filters
                </button>
              @else
                <h5 class="mt-3 text-muted">No resumes yet</h5>
                <p class="text-muted">Create your first professional resume now!</p>
                <a href="{{ route('user.resumes.choose') }}" class="btn btn-primary mt-2">
                  <i class="bx bx-plus me-1"></i> Choose Template
                </a>
              @endif
            </div>
          @else
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Resume Details</th>
                    <th>Template</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th class="d-flex align-items-center" style="gap:0.25rem;">
                      <span class="d-inline-flex align-items-center" style="color: #6366f1; font-size: 1.3em;">
                        <i class="bx bx-dots-vertical-rounded"></i>
                      </span>
                      <span>Actions</span>
                    </th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($resumes as $resume)
                    <tr>
                      <td data-label="Resume Details">
                        <div class="d-flex align-items-center gap-2">
                          <i class="bx bx-file-blank me-2" style="font-size: 24px; color: #667eea;"></i>
                          <div>
                            <div class="fw-semibold">{{ $resume->name }}</div>
                            <small class="text-muted">{{ $resume->title }}</small>
                          </div>
                        </div>
                      </td>
                      <td data-label="Template">
                        <span class="badge {{ $resume->template ? 'bg-label-primary' : 'bg-label-warning' }}">
                          {{ $resume->template?->name ?? 'Template Deleted' }}
                        </span>
                      </td>
                      <td data-label="Status">
                        @if($resume->status === 'completed')
                          <span class="badge bg-label-success">
                            <i class="bx bx-check-circle"></i> Completed
                          </span>
                        @elseif($resume->status === 'pending')
                          <span class="badge bg-label-warning">
                            <i class="bx bx-time"></i> Pending
                          </span>
                        @else
                          <span class="badge bg-label-secondary">
                            {{ ucfirst($resume->status) }}
                          </span>
                        @endif
                      </td>
                      <td data-label="Created">
                        <small class="text-muted">
                          {{ $resume->created_at->format('M d, Y') }}<br>
                          <span class="text-muted">{{ $resume->created_at->format('h:i A') }}</span>
                        </small>
                      </td>
                      <td data-label="Actions">
                        <div class="dropdown position-static">
                          <button class="btn btn-sm resume-actions-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Actions">
                            <i class="bx bx-dots-vertical-rounded"></i>
                          </button>
                          <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                              <a class="dropdown-item" href="{{ route('user.resumes.view', $resume->id) }}" target="_blank">
                                <i class="bx bx-show me-2"></i> View PDF
                              </a>
                            </li>
                            @if($hasActivePackage)
                            <li>
                              <a class="dropdown-item" href="{{ route('user.resumes.download', $resume->id) }}">
                                <i class="bx bx-download me-2"></i> Download PDF
                              </a>
                            </li>
                            @endif
                            <li>
                              <a class="dropdown-item" href="{{ route('user.resumes.fill', $resume->template_id) }}">
                                <i class="bx bx-copy me-2"></i> Create Similar
                              </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                              <form action="{{ route('user.resumes.destroy', $resume->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this resume?')" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item text-danger">
                                  <i class="bx bx-trash me-2"></i> Delete
                                </button>
                              </form>
                            </li>
                          </ul>
                        </div>
                      </td>
                    </tr>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            @if($resumes->hasPages())
            <div class="mt-4 d-flex justify-content-center">
              <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm mb-0">
                  @foreach(range(1, $resumes->lastPage()) as $page)
                    @if($page == $resumes->currentPage())
                      <li class="page-item active">
                        <span class="page-link">{{ $page }}</span>
                      </li>
                    @else
                      <li class="page-item">
                        <a class="page-link" href="{{ $resumes->url($page) }}">{{ $page }}</a>
                      </li>
                    @endif
                  @endforeach
                </ul>
              </nav>
            </div>
            @endif
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- Filter JavaScript -->
  <script>
    let searchTimeout;

    // Search with debounce
    document.getElementById('searchInput')?.addEventListener('input', function(e) {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        applyFilters();
      }, 500);
    });

    function applyFilters() {
      const status = document.getElementById('statusFilter').value;
      const sortBy = document.getElementById('sortFilter').value;
      const search = document.getElementById('searchInput').value;

      const params = new URLSearchParams();
      if (status && status !== 'all') params.append('status', status);
      if (sortBy && sortBy !== 'latest') params.append('sort_by', sortBy);
      if (search) params.append('search', search);

      window.location.href = '{{ route("user.resumes.index") }}' + (params.toString() ? '?' + params.toString() : '');
    }

    function clearFilters() {
      window.location.href = '{{ route("user.resumes.index") }}';
    }
  </script>

  <!-- Mobile Responsive Styles -->
  <style>
        /* Resume actions button (3 dots) color and desktop style */
        .resume-actions-btn {
          color: #6366f1 !important;
          font-size: 1.3em;
          background: transparent;
          border: none;
          box-shadow: none;
          padding: 0.25rem 0.5rem;
          cursor: pointer;
          display: flex;
          align-items: center;
          justify-content: center;
          white-space: nowrap;
        }
        
        .resume-actions-btn:hover {
          background-color: rgba(99, 102, 241, 0.1) !important;
        }
        
        /* Dropdown positioning */
        .dropdown.position-static {
          position: relative !important;
        }
        
        /* Desktop dropdown - right aligned */
        .dropdown-menu-end {
          right: 0 !important;
          left: auto !important;
        }
        
        .dropdown-menu {
          min-width: 200px;
          z-index: 1050;
        }
        
        @media (max-width: 768px) {
          /* Hide Actions column header and data on mobile */
          .table thead th:last-child {
            display: none;
          }
          
          .table tbody td[data-label="Actions"] {
            display: none;
          }
          
          /* Show dropdown inside resume details on mobile */
          .table tbody td[data-label="Resume Details"] {
            position: relative;
          }
          
          .table tbody td[data-label="Resume Details"] .dropdown {
            display: block;
            position: absolute;
            right: 0;
            top: 0;
          }
          
          .resume-actions-btn {
            background: #6366f1 !important;
            color: #fff !important;
            border-radius: 50%;
            width: 2.2rem;
            height: 2.2rem;
            padding: 0 !important;
          }
          
          .resume-actions-btn i.bx-dots-vertical-rounded {
            color: #fff !important;
            font-size: 1.4em !important;
          }
          
          .dropdown-menu {
            min-width: 150px;
            position: absolute !important;
            left: -120px !important;
            right: auto !important;
            z-index: 1050 !important;
          }
        }
        
        @media (max-width: 576px) {
          .dropdown-menu {
            min-width: 140px;
            left: -110px !important;
          }
        }
            right: auto !important;
            z-index: 1050 !important;
          }
        }
    .pagination-sm .page-link {
      padding: 0.4rem 0.7rem;
      font-size: 0.875rem;
      border-radius: 0.25rem;
      margin: 0 0.15rem;
    }

    .pagination-sm .page-item.active .page-link {
      background-color: #667eea;
      border-color: #667eea;
    }

    @media (max-width: 768px) {
      .pagination-sm .page-link {
        padding: 0.35rem 0.6rem;
        font-size: 0.8rem;
      }

      .pagination {
        gap: 0.1rem;
      }

      .pagination .page-item:first-child .page-link,
      .pagination .page-item:last-child .page-link {
        padding: 0.4rem 0.5rem;
      }

      .pagination svg,
      .pagination .page-link svg,
      nav[role="navigation"] svg,
      nav[aria-label="Pagination Navigation"] svg {
        width: 12px !important;
        height: 12px !important;
        max-width: 12px !important;
        max-height: 12px !important;
      }

      .card-header {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 0.5rem;
      }

      .card-header .btn {
        width: 100%;
      }

      .table-responsive {
        font-size: 0.85rem;
      }

      .table thead {
        display: none;
      }

      .table tbody tr {
        display: block;
        margin-bottom: 1rem;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 1rem;
      }

      .table tbody td {
        display: block;
        text-align: left;
        padding: 0.5rem 0;
        border: none;
      }

      .table tbody td:before {
        content: attr(data-label);
        font-weight: bold;
        display: inline-block;
        margin-right: 0.5rem;
      }

      .table tbody td:first-child:before {
        content: '';
      }
    }

    @media (max-width: 576px) {
      .dropdown-menu {
        font-size: 0.85rem;
      }

      .badge {
        font-size: 0.7rem;
      }

      /* Hide page numbers on very small screens, keep only prev/next */
      .pagination .page-item:not(:first-child):not(:last-child) {
        display: none;
      }

      .pagination .page-item.active {
        display: inline-block !important;
      }
    }
  </style>
</x-layouts.app>
