@foreach ($transactions as $transaction)
    <tr>
        <td class="text-center">
            @if ($transaction->amount_type == 'add')
                <i class="ki-duotone ki-arrow-up fs-2 text-success me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            @elseif ($transaction->amount_type == 'minus')
                <i class="ki-duotone ki-arrow-down fs-2 text-danger me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            @endif
        </td>
        <td class="text-center">{{ $transaction->description }}</td>
        <td class="text-center">{{ $transaction->created_at }}</td>
        <td class="text-center">
            {{ $transaction->customer_order?->order_id ?? '-' }}
        </td>
        <td class="text-center">
            @if ($transaction->amount_type == 'add')
                <div class="badge badge-light-success fs-6">
                    + {{ $transaction->net_amount }}
                </div>
            @elseif ($transaction->amount_type == 'minus')
                <div class="badge badge-light-danger fs-6">
                    - {{ $transaction->net_amount }}
                </div>
            @endif
        </td>
        <td class="text-center">
            <div class="badge badge-light-info fs-6">
                {{ $transaction->current_balance }}
            </div>
        </td>
    </tr>
@endforeach
