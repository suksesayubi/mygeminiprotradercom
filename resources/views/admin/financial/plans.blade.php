@extends('layouts.admin')

@section('title', 'Subscription Plans')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Subscription Plans</h3>
                    <a href="{{ route('admin.financial.plans.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Plan
                    </a>
                </div>
                <div class="card-body">
                    @if($plans->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Billing Period</th>
                                        <th>Status</th>
                                        <th>Featured</th>
                                        <th>Sort Order</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($plans as $plan)
                                        <tr>
                                            <td>
                                                <strong>{{ $plan->name }}</strong>
                                                @if($plan->description)
                                                    <br><small class="text-muted">{{ $plan->description }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-success">
                                                    {{ $plan->currency }} {{ number_format($plan->price, 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ ucfirst($plan->billing_period) }}</span>
                                            </td>
                                            <td>
                                                @if($plan->is_active)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($plan->is_featured)
                                                    <span class="badge badge-warning">Featured</span>
                                                @else
                                                    <span class="badge badge-light">Normal</span>
                                                @endif
                                            </td>
                                            <td>{{ $plan->sort_order }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.financial.plans.edit', $plan) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            onclick="deletePlan({{ $plan->id }})">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                            <h4>No Plans Found</h4>
                            <p class="text-muted">Create your first subscription plan to get started.</p>
                            <a href="{{ route('admin.financial.plans.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create Plan
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function deletePlan(planId) {
    if (confirm('Are you sure you want to delete this plan?')) {
        // Add delete functionality here
        console.log('Delete plan:', planId);
    }
}
</script>
@endpush
@endsection
