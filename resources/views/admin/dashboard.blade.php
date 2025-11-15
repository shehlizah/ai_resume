<x-layouts.app :title="__('Admin Dashboard')">

@section('content')
<div class="container text-center py-5">
    <h2>Admin Dashboard</h2>
    <p>Welcome back, {{ Auth::user()->name }}!</p>
    <p>Manage templates, users, and more here.</p>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-danger mt-3">Logout</button>
    </form>
</div>
@endsection
</x-layouts.app>
