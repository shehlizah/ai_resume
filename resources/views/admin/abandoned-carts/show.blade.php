@extends('components.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  
  <!-- Page Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="fw-bold mb-0">
        <a href="{{ route('admin.abandoned-carts.index') }}" class="text-muted">
          <i class="bx bx-arrow-back"></i>
        </a>
        <span class="text-muted fw-light">Abandoned Carts /</span> Details
      </h4>
    </div>
    <span class="badge bg-{{ $cart->status === 'abandoned' ? 'danger' : ($cart->status === 'completed' ? 'success' : 'info') }} badge-lg">
      {{ ucfirst($cart->status) }}
    </span>
  </div>

  <div class="row">
    <!-- Cart Information -->
    <div class="col-md-8">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">Cart Information</h5>
        </div>
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-sm-3">
              <strong>Type:</strong>
            </div>
            <div class="col-sm-9">
              <span class="badge bg-label-{{ $cart->type === 'signup' ? 'info' : ($cart->type === 'resume' ? 'primary' : 'warning') }}">
                {{ ucfirst(str_replace('_', ' ', $cart->type)) }}
              </span>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-sm-3">
              <strong>Status:</strong>
            </div>
            <div class="col-sm-9">
              <span class="badge bg-{{ $cart->status === 'abandoned' ? 'danger' : ($cart->status === 'completed' ? 'success' : 'info') }}">
                {{ ucfirst($cart->status) }}
              </span>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-sm-3">
              <strong>Created:</strong>
            </div>
            <div class="col-sm-9">
              {{ $cart->created_at->format('M d, Y H:i:s') }}
              <small class="text-muted">({{ $cart->created_at->diffForHumans() }})</small>
            </div>
          </div>

          @if($cart->completed_at)
          <div class="row mb-3">
            <div class="col-sm-3">
              <strong>Completed:</strong>
            </div>
            <div class="col-sm-9">
              {{ $cart->completed_at->format('M d, Y H:i:s') }}
              <small class="text-muted">({{ $cart->completed_at->diffForHumans() }})</small>
            </div>
          </div>
          @endif

          <div class="row mb-3">
            <div class="col-sm-3">
              <strong>Recovery Emails:</strong>
            </div>
            <div class="col-sm-9">
              <span class="badge bg-label-secondary">{{ $cart->recovery_email_sent_count }} sent</span>
              @if($cart->first_recovery_email_at)
                <br><small class="text-muted">First sent: {{ $cart->first_recovery_email_at->format('M d, Y H:i') }}</small>
              @endif
            </div>
          </div>

          @if($cart->resume_id)
          <div class="row mb-3">
            <div class="col-sm-3">
              <strong>Resume ID:</strong>
            </div>
            <div class="col-sm-9">
              <code>{{ $cart->resume_id }}</code>
            </div>
          </div>
          @endif
        </div>
      </div>

      <!-- Session Data -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">Session Data</h5>
        </div>
        <div class="card-body">
          @php
            $sessionData = is_string($cart->session_data) ? json_decode($cart->session_data, true) : $cart->session_data;
          @endphp
          
          @if($sessionData && is_array($sessionData))
            <table class="table table-sm">
              @foreach($sessionData as $key => $value)
              <tr>
                <td class="fw-bold text-capitalize" style="width: 30%">{{ str_replace('_', ' ', $key) }}</td>
                <td>
                  @if(is_array($value))
                    <pre class="mb-0">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                  @else
                    {{ $value }}
                  @endif
                </td>
              </tr>
              @endforeach
            </table>
          @else
            <p class="text-muted">No session data available</p>
          @endif
        </div>
      </div>
    </div>

    <!-- User Information -->
    <div class="col-md-4">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">User Information</h5>
        </div>
        <div class="card-body">
          @if($cart->user)
            <div class="text-center mb-3">
              <div class="avatar avatar-xl mb-3">
                <span class="avatar-initial rounded-circle bg-label-primary">
                  {{ strtoupper(substr($cart->user->name, 0, 2)) }}
                </span>
              </div>
              <h5 class="mb-1">{{ $cart->user->name }}</h5>
              <p class="text-muted mb-0">{{ $cart->user->email }}</p>
            </div>

            <hr>

            <div class="mb-2">
              <strong>User ID:</strong> <code>{{ $cart->user->id }}</code>
            </div>
            <div class="mb-2">
              <strong>Role:</strong> 
              <span class="badge bg-label-{{ $cart->user->role === 'admin' ? 'danger' : 'primary' }}">
                {{ ucfirst($cart->user->role) }}
              </span>
            </div>
            <div class="mb-2">
              <strong>Status:</strong> 
              <span class="badge bg-label-{{ $cart->user->is_active ? 'success' : 'secondary' }}">
                {{ $cart->user->is_active ? 'Active' : 'Inactive' }}
              </span>
            </div>
            <div class="mb-2">
              <strong>Joined:</strong> {{ $cart->user->created_at->format('M d, Y') }}
            </div>

            <hr>

            <a href="{{ url('admin/users/' . $cart->user->id) }}" class="btn btn-sm btn-outline-primary w-100">
              <i class="bx bx-user"></i> View User Profile
            </a>
          @else
            <div class="text-center text-muted py-4">
              <i class="bx bx-user-x bx-lg mb-3"></i>
              <p>No user associated</p>
              @php
                $sessionData = is_string($cart->session_data) ? json_decode($cart->session_data, true) : $cart->session_data;
              @endphp
              @if(isset($sessionData['email']))
                <small>Email: {{ $sessionData['email'] }}</small>
              @endif
            </div>
          @endif
        </div>
      </div>

      <!-- Actions -->
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Actions</h5>
        </div>
        <div class="card-body">
          @if($cart->status === 'abandoned' && $cart->user)
          <form action="{{ route('admin.abandoned-carts.send-reminder', $cart->id) }}" method="POST" class="mb-2">
            @csrf
            <button type="submit" class="btn btn-primary w-100">
              <i class="bx bx-envelope"></i> Send Reminder Email
            </button>
          </form>
          @endif

          @if($cart->status === 'abandoned')
          <form action="{{ route('admin.abandoned-carts.mark-completed', $cart->id) }}" method="POST" class="mb-2">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-success w-100">
              <i class="bx bx-check"></i> Mark as Completed
            </button>
          </form>
          @endif

          <form action="{{ route('admin.abandoned-carts.destroy', $cart->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this record?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger w-100">
              <i class="bx bx-trash"></i> Delete Record
            </button>
          </form>
        </div>
      </div>
    </div>
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
@endsection
