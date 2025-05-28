@extends('layouts.admin')

@section('title', 'Transactions')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payment Transactions</h3>
                </div>
                <div class="card-body">
                    @if(isset($transactions) && $transactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Payment Method</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->id }}</td>
                                            <td>
                                                @if($transaction->user)
                                                    {{ $transaction->user->name }}
                                                    <br><small class="text-muted">{{ $transaction->user->email }}</small>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-success">
                                                    ${{ number_format($transaction->amount, 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                @switch($transaction->status)
                                                    @case('completed')
                                                        <span class="badge badge-success">Completed</span>
                                                        @break
                                                    @case('pending')
                                                        <span class="badge badge-warning">Pending</span>
                                                        @break
                                                    @case('failed')
                                                        <span class="badge badge-danger">Failed</span>
                                                        @break
                                                    @default
                                                        <span class="badge badge-secondary">{{ ucfirst($transaction->status) }}</span>
                                                @endswitch
                                            </td>
                                            <td>{{ $transaction->payment_method ?? 'N/A' }}</td>
                                            <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" onclick="viewTransaction({{ $transaction->id }})">
                                                    <i class="fas fa-eye"></i> View
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if(method_exists($transactions, 'links'))
                            <div class="d-flex justify-content-center">
                                {{ $transactions->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                            <h4>No Transactions Found</h4>
                            <p class="text-muted">No payment transactions have been recorded yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function viewTransaction(transactionId) {
    // Add view transaction functionality here
    console.log('View transaction:', transactionId);
}
</script>
@endpush
@endsection
