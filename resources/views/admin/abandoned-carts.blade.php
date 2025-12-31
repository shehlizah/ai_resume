@extends('admin.dashboard')

@section('content')
<div class="container py-4">
    <h2>Abandoned Carts</h2>
    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>User Name</th>
                <th>User Email</th>
                <th>Type</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
        @foreach($abandonedCarts as $cart)
            <tr>
                <td>{{ $cart->user->name ?? '-' }}</td>
                <td>{{ $cart->user->email ?? ($cart->session_data['email'] ?? '-') }}</td>
                <td>{{ ucfirst($cart->type) }}</td>
                <td>{{ $cart->created_at->format('Y-m-d H:i') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="mt-3">
        <strong>Total Abandoned:</strong> {{ $abandonedCarts->count() }}
    </div>
</div>
@endsection
