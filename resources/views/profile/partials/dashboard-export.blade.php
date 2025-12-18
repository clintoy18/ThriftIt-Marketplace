<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Dashboard Report - {{ $user->fname }} {{ $user->lname }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #4f46e5;
            margin: 0 0 5px 0;
            font-size: 24px;
        }
        .header p {
            color: #666;
            margin: 0;
        }
        .user-info {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .user-info h3 {
            margin: 0 0 10px 0;
            color: #4f46e5;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }
        .stat-row {
            display: table-row;
        }
        .stat-box {
            display: table-cell;
            width: 25%;
            padding: 15px;
            text-align: center;
            border: 1px solid #e5e7eb;
            background: #fff;
        }
        .stat-box h4 {
            margin: 0 0 5px 0;
            color: #6b7280;
            font-size: 10px;
            text-transform: uppercase;
        }
        .stat-box .value {
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
        }
        .section {
            margin-bottom: 25px;
        }
        .section h3 {
            color: #4f46e5;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: left;
        }
        table th {
            background: #f3f4f6;
            font-weight: bold;
        }
        table tr:nth-child(even) {
            background: #f9fafb;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #9ca3af;
            font-size: 10px;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Dashboard Report</h1>
        <p>Generated on {{ now()->format('F d, Y') }}</p>
    </div>

    <div class="user-info">
        <h3>User Information</h3>
        <p><strong>Name:</strong> {{ $user->fname }} {{ $user->lname }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Member Since:</strong> {{ $user->created_at->format('F d, Y') }}</p>
    </div>

    <div class="section">
        <h3>Statistics Summary</h3>
        @if(!$isUpcycler)
            <table>
                <tr>
                    <th>Total Listings</th>
                    <th>Items Sold</th>
                    <th>Items Donated</th>
                    <th>Total Revenue</th>
                </tr>
                <tr>
                    <td style="text-align: center; font-weight: bold;">{{ $totalListings }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $itemsSold }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $itemsDonated }}</td>
                    <td style="text-align: center; font-weight: bold;">₱{{ number_format($revenue, 2) }}</td>
                </tr>
            </table>
        @else
            <table>
                <tr>
                    <th>Approved Works</th>
                    <th>Completed Appointments</th>
                </tr>
                <tr>
                    <td style="text-align: center; font-weight: bold;">{{ $approvedWorks }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $completedAppointmentsAsUpcyclerCount }}</td>
                </tr>
            </table>
        @endif
    </div>

    @if(!$isUpcycler && $soldProducts->count() > 0)
    <div class="section">
        <h3>Recent Sold Items</h3>
        <table>
            <tr>
                <th>Product Name</th>
                <th>Price</th>
                <th>Date Sold</th>
            </tr>
            @foreach($soldProducts as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>₱{{ number_format($product->price, 2) }}</td>
                <td>{{ $product->updated_at->format('M d, Y') }}</td>
            </tr>
            @endforeach
        </table>
    </div>
    @endif

    @if(!$isUpcycler && $donations->count() > 0)
    <div class="section">
        <h3>Recent Donations</h3>
        <table>
            <tr>
                <th>Item Name</th>
                <th>Category</th>
                <th>Date Donated</th>
            </tr>
            @foreach($donations as $donation)
            <tr>
                <td>{{ $donation->name }}</td>
                <td>{{ $donation->category->name ?? 'N/A' }}</td>
                <td>{{ $donation->updated_at->format('M d, Y') }}</td>
            </tr>
            @endforeach
        </table>
    </div>
    @endif

    <div class="footer">
        <p>This report was automatically generated by Thrift-It</p>
    </div>
</body>
</html>

