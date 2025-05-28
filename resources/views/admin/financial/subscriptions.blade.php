@extends('layouts.admin')

@section('title', 'Subscriptions')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">User Subscriptions</h3>
                </div>
                <div class="card-body">
                    @if(isset($subscriptions) && $subscriptions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Plan</th>
                                        <th>Status</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subscriptions as $subscription)
                                        <tr>
                                            <td>{{ $subscription->id }}</td>
                                            <td>
                                                @if($subscription->user)
                                                    {{ $subscription->user->name }}
                                                    <br><small class="text-muted">{{ $subscription->user->email }}</small>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($subscription->plan)
                                                    {{ $subscription->plan->name }}
                                                    <br><small class="text-muted">${{ number_format($subscription->plan->price, 2) }}/{{ $subscription->plan->billing_period }}</small>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @switch($subscription->status)
                                                    @case('active')
                                                        <span class="badge badge-success">Active</span>
                                                        @break
                                                    @case('cancelled')
                                                        <span class="badge badge-danger">Cancelled</span>
                                                        @break
                                                    @case('expired')
                                                        <span class="badge badge-warning">Expired</span>
                                                        @break
                                                    @default
                                                        <span class="badge badge-secondary">{{ ucfirst($subscription->status) }}</span>
                                                @endswitch
                                            </td>
                                            <td>{{ $subscription->start_date ? $subscription->start_date->format('M d, Y') : 'N/A' }}</td>
                                            <td>{{ $subscription->end_date ? $subscription->end_date->format('M d, Y') : 'N/A' }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-outline-primary" onclick="viewSubscription({{ $subscription->id }})">
                                                        <i class="fas fa-eye"></i> View
                                                    </button>
                                                    @if($subscription->status === 'active')
                                                        <button class="btn btn-sm btn-outline-warning" onclick="cancelSubscription({{ $subscription->id }})">
                                                            <i class="fas fa-ban"></i> Cancel
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if(method_exists($subscriptions, 'links'))
                            <div class="d-flex justify-content-center">
                                {{ $subscriptions->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h4>No Subscriptions Found</h4>
                            <p class="text-muted">No user subscriptions have been created yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function viewSubscription(subscriptionId) {
    // Add view subscription functionality here
    console.log('View subscription:', subscriptionId);
}

function cancelSubscription(subscriptionId) {
    if (confirm('Are you sure you want to cancel this subscription?')) {
        // Add cancel subscription functionality here
        console.log('Cancel subscription:', subscriptionId);
    }
}
</script>
@endpush
@endsection
