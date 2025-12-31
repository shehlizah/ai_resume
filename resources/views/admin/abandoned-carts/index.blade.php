<x-layouts.app>
<div class="container-xxl flex-grow-1 container-p-y">

  <!-- Page Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
      <span class="text-muted fw-light">Admin /</span> Abandoned Carts
    </h4>
  </div>

  <!-- Statistics Cards -->
  <div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="text-muted mb-1">Total Abandoned</h6>
              <h3 class="mb-0">{{ $stats['total_abandoned'] }}</h3>
            </div>
            <div class="avatar">
              <span class="avatar-initial rounded bg-label-danger">
                <i class="bx bx-cart bx-sm"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="text-muted mb-1">Recovered</h6>
              <h3 class="mb-0">{{ $stats['total_recovered'] }}</h3>
            </div>
            <div class="avatar">
              <span class="avatar-initial rounded bg-label-success">
                <i class="bx bx-check-circle bx-sm"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="text-muted mb-1">Pending Recovery</h6>
              <h3 class="mb-0">{{ $stats['pending_recovery'] }}</h3>
            </div>
            <div class="avatar">
              <span class="avatar-initial rounded bg-label-warning">
                <i class="bx bx-time-five bx-sm"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="text-muted mb-1">Recovery Rate</h6>
              <h3 class="mb-0">
                @if($stats['total_abandoned'] > 0)
                  {{ round(($stats['total_recovered'] / $stats['total_abandoned']) * 100, 1) }}%
                @else
                  0%
                @endif
              </h3>
            </div>
            <div class="avatar">
              <span class="avatar-initial rounded bg-label-info">
                <i class="bx bx-trending-up bx-sm"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Breakdown by Type -->
  <div class="card mb-4">
    <div class="card-body">
      <h5 class="card-title mb-3">Abandonment by Type</h5>
      <div class="row">
        @foreach($stats['by_type'] as $type => $count)
        <div class="col-md-4 mb-3">
          <div class="d-flex align-items-center">
            <div class="me-3">
              <i class="bx bx-{{ $type === 'signup' ? 'user-plus' : ($type === 'resume' ? 'file' : 'download') }} text-primary" style="font-size: 2rem;"></i>
            </div>
            <div>
              <h6 class="mb-0 text-capitalize">{{ str_replace('_', ' ', $type) }}</h6>
              <span class="text-muted">{{ $count }} abandoned</span>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>

  <!-- Filters -->
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('admin.abandoned-carts.index') }}">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All Statuses</option>
              <option value="abandoned" {{ request('status') === 'abandoned' ? 'selected' : '' }}>Abandoned</option>
              <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
              <option value="recovered" {{ request('status') === 'recovered' ? 'selected' : '' }}>Recovered</option>
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label">Type</label>
            <select name="type" class="form-select">
              <option value="all" {{ request('type') === 'all' ? 'selected' : '' }}>All Types</option>
              <option value="signup" {{ request('type') === 'signup' ? 'selected' : '' }}>Signup</option>
              <option value="resume" {{ request('type') === 'resume' ? 'selected' : '' }}>Resume</option>
              <option value="pdf_preview" {{ request('type') === 'pdf_preview' ? 'selected' : '' }}>PDF Preview</option>
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control" placeholder="Email or name..." value="{{ request('search') }}">
          </div>

          <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Abandoned Carts Table -->
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Abandoned Carts List</h5>
      <span class="badge bg-label-primary">{{ $carts->total() }} total</span>
    </div>
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>User</th>
            <th>Type</th>
            <th>Status</th>
            <th>Emails Sent</th>
            <th>Created</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($carts as $cart)
          <tr>
            <td>
              @if($cart->user)
                <div>
                  <strong>{{ $cart->user->name }}</strong><br>
                  <small class="text-muted">{{ $cart->user->email }}</small>
                </div>
              @else
                <div>
                  @php
                    $sessionData = is_string($cart->session_data) ? json_decode($cart->session_data, true) : $cart->session_data;
                  @endphp
                  <span class="text-muted">{{ $sessionData['email'] ?? 'No email' }}</span>
                </div>
              @endif
            </td>
            <td>
              <span class="badge bg-label-{{ $cart->type === 'signup' ? 'info' : ($cart->type === 'resume' ? 'primary' : 'warning') }}">
                {{ ucfirst(str_replace('_', ' ', $cart->type)) }}
              </span>
            </td>
            <td>
              <span class="badge bg-{{ $cart->status === 'abandoned' ? 'danger' : ($cart->status === 'completed' ? 'success' : 'info') }}">
                {{ ucfirst($cart->status) }}
              </span>
            </td>
            <td>
              <span class="badge bg-label-secondary">{{ $cart->recovery_email_sent_count }}</span>
            </td>
            <td>
              <small>{{ $cart->created_at->diffForHumans() }}</small><br>
              <small class="text-muted">{{ $cart->created_at->format('M d, Y H:i') }}</small>
            </td>
            <td>
              <div class="dropdown">
                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                  Actions
                </button>
                <ul class="dropdown-menu">
                  <li>
                    <a class="dropdown-item" href="{{ route('admin.abandoned-carts.show', $cart->id) }}">
                      <i class="bx bx-show me-1"></i> View Details
                    </a>
                  </li>
                  @if($cart->status === 'abandoned' && $cart->user)
                  <li>
                    <form action="{{ route('admin.abandoned-carts.send-reminder', $cart->id) }}" method="POST" class="d-inline">
                      @csrf
                      <button type="submit" class="dropdown-item">
                        <i class="bx bx-envelope me-1"></i> Send Reminder
                      </button>
                    </form>
                  </li>
                  @endif
                  @if($cart->status === 'abandoned')
                  <li>
                    <form action="{{ route('admin.abandoned-carts.mark-completed', $cart->id) }}" method="POST" class="d-inline">
                      @csrf
                      @method('PATCH')
                      <button type="submit" class="dropdown-item">
                        <i class="bx bx-check me-1"></i> Mark Completed
                      </button>
                    </form>
                  </li>
                  @endif
                  <li><hr class="dropdown-divider"></li>
                  <li>
                    <form action="{{ route('admin.abandoned-carts.destroy', $cart->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="dropdown-item text-danger">
                        <i class="bx bx-trash me-1"></i> Delete
                      </button>
                    </form>
                  </li>
                </ul>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="text-center py-5">
              <i class="bx bx-cart bx-lg text-muted mb-3"></i>
              <p class="text-muted">No abandoned carts found.</p>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($carts->hasPages())
    <div class="card-footer">
      {{ $carts->links() }}
    </div>
    @endif
  </div>

</div>

@if(session('success'))
<script>
  document.addEventListener('DOMContentLoaded', function() {
    alert('{{ session('success') }}');
  });
</script>
@endif

@if(session('error'))
<script>
  document.addEventListener('DOMContentLoaded', function() {
    alert('{{ session('error') }}');
  });
</script>
@endif
</x-layouts.app>
