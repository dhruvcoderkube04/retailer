@foreach ($transactions as $transaction)
    <tr>
        <td class="text-center">
            @if ($transaction->amount_type == 'add')
                <i class="ki-duotone ki-arrow-up fs-1 text-success me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            @elseif ($transaction->amount_type == 'minus')
                <i class="ki-duotone ki-arrow-down fs-1 text-danger me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            @endif
        </td>
        <td class="text-start">{{ $transaction->description }}</td>
        <td class="text-center">{{ $transaction->created_at }}</td>
        <td class="text-center">
            {{ $transaction->customer_order?->order_id ?? '-' }}
        </td>
        <td class="text-end">
            @if ($transaction->amount_type == 'add')
                <div class="badge badge-light-success fs-6">
                    + {{ $transaction->total_amount }}
                </div>
            @elseif ($transaction->amount_type == 'minus')
                <div class="badge badge-light-danger fs-6">
                    - {{ $transaction->total_amount }}
                </div>
            @endif
        </td>
        <td class="text-end">
            <div class="badge badge-light-secondary fs-6">
                {{ $transaction->current_balance }}
            </div>
        </td>
        <td class="text-center">
            <a href="javascript:void(0)" class="transaction-info" data-id="{{ $transaction->id }}">
                <i class="ki-duotone ki-information-2 fs-2 text-primary">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
            </a>
        </td>
    </tr>
@endforeach
