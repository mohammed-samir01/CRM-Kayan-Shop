<!DOCTYPE html>
<html lang="ar" dir="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>فاتورة طلب #{{ $order->id }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap');

        body {
            font-family: 'Amiri', 'DejaVu Sans', sans-serif;
            direction: ltr;
            text-align: right;
            font-size: 14px;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .header p {
            margin: 5px 0 0;
            color: #666;
        }

        .info-section {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 5px;
            vertical-align: top;
        }

        .label {
            font-weight: bold;
            color: #555;
            width: 120px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .items-table th, .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        .items-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .items-table td:last-child {
            text-align: right;
        }

        .totals {
            width: 100%;
            margin-top: 20px;
        }

        .totals-table {
            width: 300px;
            float: left;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        .totals-table .total-row td {
            border-top: 2px solid #000;
            font-weight: bold;
            font-size: 16px;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .status-badge {
            padding: 2px 8px;
            border-radius: 4px;
            background-color: #eee;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name', 'Laravel') }}</h1>
        <p>{{ fix_arabic('فاتورة طلب') }}</p>
    </div>

    <table class="info-section">
        <tr>
            <td width="50%">
                <h3 style="margin-top: 0; margin-bottom: 10px;">{{ fix_arabic('معلومات الطلب') }}</h3>
                <table class="info-table">
                    <tr>
                        <td class="label">{{ fix_arabic('رقم الطلب:') }}</td>
                        <td>#{{ $order->id }}</td>
                    </tr>
                    <tr>
                        <td class="label">{{ fix_arabic('تاريخ الطلب:') }}</td>
                        <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="label">{{ fix_arabic('حالة الطلب:') }}</td>
                        <td>{{ fix_arabic(__('orders.status.' . $order->order_status)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">{{ fix_arabic('طريقة الدفع:') }}</td>
                        <td>{{ fix_arabic(__('orders.payment_method.' . $order->payment_method)) }}</td>
                    </tr>
                </table>
            </td>
            <td width="50%">
                <h3 style="margin-top: 0; margin-bottom: 10px;">{{ fix_arabic('معلومات العميل') }}</h3>
                <table class="info-table">
                    <tr>
                        <td class="label">{{ fix_arabic('الاسم:') }}</td>
                        <td>{{ fix_arabic($order->lead->customer_name) }}</td>
                    </tr>
                    <tr>
                        <td class="label">{{ fix_arabic('رقم الهاتف:') }}</td>
                        <td>{{ $order->lead->phone }}</td>
                    </tr>
                    <tr>
                        <td class="label">{{ fix_arabic('المدينة:') }}</td>
                        <td>{{ fix_arabic($order->lead->city ?? '-') }}</td>
                    </tr>
                    <tr>
                        <td class="label">{{ fix_arabic('العنوان:') }}</td>
                        <td>{{ fix_arabic($order->lead->address ?? '-') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>{{ fix_arabic('الإجمالي') }}</th>
                <th>{{ fix_arabic('سعر الوحدة') }}</th>
                <th>{{ fix_arabic('الكمية') }}</th>
                <th>{{ fix_arabic('اللون') }}</th>
                <th>{{ fix_arabic('المقاس') }}</th>
                <th>{{ fix_arabic('المتغير') }}</th>
                <th>{{ fix_arabic('المنتج') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ number_format($item->line_total, 2) }}</td>
                <td>{{ number_format($item->unit_price, 2) }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ fix_arabic($item->color ?? '-') }}</td>
                <td>{{ fix_arabic($item->size ?? '-') }}</td>
                <td>{{ fix_arabic($item->variant ?? '-') }}</td>
                <td>{{ fix_arabic($item->product_name) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table class="totals-table">
            <tr class="total-row">
                <td>{{ fix_arabic('إجمالي الطلب:') }}</td>
                <td>{{ number_format($order->total_value, 2) }} {{ fix_arabic('ريال') }}</td>
            </tr>
        </table>
        <div style="clear: both;"></div>
    </div>

    <div class="footer">
        @if($order->notes)
            <p><strong>{{ fix_arabic('ملاحظات:') }}</strong> {{ fix_arabic($order->notes) }}</p>
        @endif
        <p>{{ fix_arabic('شكراً لتعاملكم معنا') }}</p>
    </div>
</body>
</html>
