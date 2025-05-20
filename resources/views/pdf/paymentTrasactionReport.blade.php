<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>Transaction</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<style>
    @page {
        @top-center {
            content: "Oficina: {{ $office_name }}";
            font-size: 14px;
            font-weight: bold;
        }
    }

    .overflow-x-auto {
        overflow-x: auto;
    }

    .p-4 {
        padding: 1rem;
    }

    .table-auto {
        table-layout: auto;
    }

    .w-full {
        width: 100%;
    }

    .text-sm {
        font-size: 0.875rem;
    }

    .text-8px {
        font-size: 8px;
    }

    .border {
        border: 1px solid #e2e8f0;
    }

    .p-1 {
        padding: 0.25rem;
    }

    .row-span-6 {
        grid-row: span 6 / span 6;
    }

    .border-t {
        border-top: 1px solid #e2e8f0;
    }

    .border-x {
        border-left: 1px solid #e2e8f0;
        border-right: 1px solid #e2e8f0;
    }

    .text-center {
        text-align: center;
    }

    .border-b {
        border-bottom: 1px solid #e2e8f0;
    }

    .border-b-transparent {
        border-bottom: 1px solid transparent;
    }

    .w-24 {
        width: 6rem;
    }

    .border-l {
        border-left: 1px solid #e2e8f0;
    }

    .font-bold {
        font-weight: 700;
    }
    .header {
            position: fixed;
            top: -60px;
            left: 0;
            width: 100%;
            font-size: 16px;
            font-weight: bold;
        }
    .text-right {
        text-align: right;
    }
    @page {
        margin-top: 80px;
    }
</style>
@php
$totals = [
'transactions_listing_res' => 0,
'transactions_selling_res' => 0,
'transactions_listing_res_volume' => 0,
'transactions_selling_res_volume' => 0,

'amount_payment_res' => 0,
'payments_in_year_res' => 0,

'transactions_listing_com' => 0,
'transactions_selling_com' => 0,
'transactions_listing_com_volume' => 0,
'transactions_selling_com_volume' => 0,
'amount_payment_com' => 0,
'payments_in_year_com' => 0,
];

foreach ($transactions as $transaction) {
$totals['transactions_listing_res'] += $transaction['transactions_listing_res'] ?? 0;
$totals['transactions_selling_res'] += $transaction['transactions_selling_res'] ?? 0;
$totals['transactions_listing_res_volume'] += $transaction['transactions_listing_res_volume'] ?? 0;
$totals['transactions_selling_res_volume'] += $transaction['transactions_selling_res_volume'] ?? 0;

$totals['amount_payment_res'] += isset($transaction['amount_payment_res']) ? floatval($transaction['amount_payment_res']) : 0;
$totals['payments_in_year_res'] += isset($transaction['payments_in_year_res']) ? floatval($transaction['payments_in_year_res']) : 0;

$totals['transactions_listing_com'] += $transaction['transactions_listing_com'] ?? 0;
$totals['transactions_selling_com'] += $transaction['transactions_selling_com'] ?? 0;
$totals['transactions_listing_com_volume'] += $transaction['transactions_listing_com_volume'] ?? 0;
$totals['transactions_selling_com_volume'] += $transaction['transactions_selling_com_volume'] ?? 0;

$totals['amount_payment_com'] += isset($transaction['amount_payment_com']) ? floatval($transaction['amount_payment_com']) : 0;
$totals['payments_in_year_com'] += isset($transaction['payments_in_year_com']) ? floatval($transaction['payments_in_year_com']) : 0;
}
@endphp


<body>
    <div class="header">
        {{ $office_name }} - Reporte mensual de comisiones {{ $month }}/{{ $year }}
    </div>

    <div class="overflow-x-auto">
        <table class="table-auto w-full">
            <thead class="text-sm">
                <tr class="">
                    <th></th>
                    <th class="row-span-6" colspan="6">Transacciones Residenciales</th>
                    <th class="row-span-6" colspan="6">Transacciones Comerciales</th>
                </tr>
                <tr class="text-8px">
                    <th class="border p-1">Nombre de asociado</th>
                    <th class="border p-1">Captaciones Finalizadas</th>
                    <th class="border p-1">Venta Finalizadas</th>
                    <th class="border p-1">Volumenes Captacion</th>
                    <th class="border p-1">Volumenes Venta</th>
                    <th class="border p-1">Comisiones</th>
                    <th class="border p-1">Comisiones a la fecha</th>
                    <th class="border p-1">Captaciones Finalizadas</th>
                    <th class="border p-1">Venta Finalizadas</th>
                    <th class="border p-1">Volumenes Captacion</th>
                    <th class="border p-1">Volumenes Venta</th>
                    <th class="border p-1">Comisiones</th>
                    <th class="border p-1">Comisiones a la fecha</th>

                </tr>
            </thead>
            <tbody class="text-8px text-center">
                @foreach ($transactions as $transaction)

                <tr class="border-t border-x">
                    <td class="w-24">{{ $transaction['agent_name'] }}</td>
                    <td>{{ $transaction['transactions_listing_res'] }}</td>
                    <td>{{ $transaction['transactions_selling_res'] }}</td>
                    <td>{{ number_format($transaction['transactions_listing_res_volume'], 2, '.', ',') }}</td>
                    <td>{{ number_format($transaction['transactions_selling_res_volume'], 2, '.', ',') }}</td>
                    <td>{{ number_format($transaction['amount_payment_res'], 2, '.', ',') }}</td>
                    <td>{{ number_format($transaction['payments_in_year_res'], 2, '.', ',') }}</td>
                    <td class="border-l">{{ $transaction['transactions_listing_com'] }}</td>
                    <td>{{ $transaction['transactions_selling_com'] }}</td>
                    <td>{{ number_format($transaction['transactions_listing_com_volume'], 2, '.', ',') }}</td>
                    <td>{{ number_format($transaction['transactions_selling_com_volume'], 2, '.', ',') }}</td>
                    <td>{{ number_format($transaction['amount_payment_com'], 2, '.', ',') }}</td>
                    <td>{{ number_format($transaction['payments_in_year_com'], 2, '.', ',') }}</td>

                </tr>
                @if(count($transaction['payments_res']) > 0 || count($transaction['payments_com']) > 0)
                <tr>
                    <td colspan="13" class="p-0">
                        <table class="w-full border-x border-b text-8px text-center">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>TRR ID</th>
                                    <th>MLS</th>
                                    <th>Precio</th>
                                    <th>Fecha vendida</th>
                                    <th>Tipo</th>
                                    <th>Agente principal TRR</th>
                                    <th>Tipo C/R</th>
                                    <th>Cant. Recibida</th>
                                    <th>Dia de pago</th>
                                    <th>Fecha recepción</th>
                                    <th>Porcentaje</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transaction['payments_res'] as $payment)
                                <tr class="">
                                    <td class="w-24 border-b-transparent border-b"></td>
                                    <td class="border-b">{{ $payment['trr_report_id'] }}</td>
                                    <td class="border-b">{{ $payment['mls_id'] }}</td>
                                    <td class="border-b">{{ number_format($payment['current_listing_price'], 2, '.', ',') }}</td>
                                    <td class="border-b">{{ $payment['sold_date'] }}</td>
                                    <td class="border-b">{{ $payment['transaction_type_id'] == 1 ? 'L' : 'S' }}</td>
                                    <td class="border-b">{{ $payment['transaction_agent_id'] == $payment['agent_id'] ? 'YES' : 'NO' }}</td>
                                    <td class="border-b">RES</td>
                                    <td class="border-b">{{ number_format($payment['amount_expected'], 2, '.', ',') }}</td>
                                    <td class="border-b">{{ $payment['expected_payment_date'] }}</td>
                                    <td class="border-b">{{ $payment['received_date'] }}</td>
                                    <td>{{ number_format($payment['commission_percentage'], 2, '.', ',') }}%</td>

                                </tr>
                                @endforeach
                                @foreach ($transaction['payments_com'] as $payment)
                                <tr>
                                    <td class="w-24 border-b-transparent"></td>
                                    <td>{{ $payment['trr_report_id'] }}</td>
                                    <td>{{ $payment['mls_id'] }}</td>
                                    <td>{{ number_format($payment['current_listing_price'], 2, '.', ',') }}</td>
                                    <td>{{ $payment['sold_date'] }}</td>
                                    <td>{{ $payment['transaction_type_id'] == 1 ? 'L' : 'S' }}</td>
                                    <td>{{ $payment['transaction_agent_id'] == $payment['agent_id'] ? 'YES' : 'NO' }}</td>
                                    <td>COM</td>
                                    <td>{{ number_format($payment['amount_expected'], 2, '.', ',') }}</td>
                                    <td>{{ $payment['expected_payment_date'] }}</td>
                                    <td>{{ $payment['received_date'] }}</td>
                                    <td>{{ number_format($payment['commission_percentage'], 2, '.', ',') }}%</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="9" class="text-right font-bold">Total: {{ number_format($transaction['amount_payment_res'] + $transaction['amount_payment_com'], 2, '.', ',') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
            <tfoot class="text-8px text-center">
                <tr>
                    <td class="font-bold w-24">Total agentes activos:</td>

                    <td>{{ $totals['transactions_listing_res'] }}</td>
                    <td>{{ $totals['transactions_selling_res'] }}</td>
                    <td>{{ number_format($totals['transactions_listing_res_volume'], 2, '.', ',') }}</td>
                    <td>{{ number_format($totals['transactions_selling_res_volume'], 2, '.', ',') }}</td>
                    <td>{{ number_format($totals['amount_payment_res'], 2, '.', ',') }}</td>
                    <td>{{ number_format($totals['payments_in_year_res'], 2, '.', ',') }}</td>
                    <td>{{ $totals['transactions_listing_com'] }}</td>
                    <td>{{ $totals['transactions_selling_com'] }}</td>
                    <td>{{ number_format($totals['transactions_listing_com_volume'], 2, '.', ',') }}</td>
                    <td>{{ number_format($totals['transactions_selling_com_volume'], 2, '.', ',') }}</td>
                    <td>{{ number_format($totals['amount_payment_com'], 2, '.', ',') }}</td>
                    <td>{{ number_format($totals['payments_in_year_com'], 2, '.', ',') }}</td>
                </tr>

            </tfoot>

        </table>
    </div>
</body>