<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly Export - {{ $monthName }} {{ $year }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        h1, h2 { margin: 0 0 8px 0; }
        h1 { font-size: 18px; font-weight: bold; }
        h2 { font-size: 14px; margin-top: 16px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; margin-bottom: 12px; }
        th, td { border: 1px solid #E5E7EB; padding: 6px; text-align: left; }
        th { background: #F3F4F6; font-weight: bold; }
        .summary-box { background: #F9FAFB; border: 1px solid #E5E7EB; padding: 8px; margin: 8px 0; }
        .summary-box strong { display: inline-block; min-width: 120px; }
    </style>
</head>
<body>
    <h1>Monthly Export Report - {{ $monthName }} {{ $year }}</h1>

    <!-- Users Summary -->
    <h2>Users Summary</h2>
    <div class="summary-box">
        <strong>Total Users:</strong> {{ $users->count() }}<br>
        <strong>Admins:</strong> {{ $usersByRole['admin'] }}<br>
        <strong>Upcyclers:</strong> {{ $usersByRole['upcycler'] }}<br>
        <strong>Regular Users:</strong> {{ $usersByRole['user'] }}
    </div>

    <h2>Users Registered</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Registered At</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $u)
            <tr>
                <td>{{ $u->fname }} {{ $u->lname }}</td>
                <td>{{ $u->email }}</td>
                <td>{{ $u->role === 2 ? 'Admin' : ($u->role === 1 ? 'Upcycler' : 'User') }}</td>
                <td>{{ $u->created_at->format('M d, Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="4">No users registered this month.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Upcyclers Registered</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Registered At</th>
            </tr>
        </thead>
        <tbody>
            @forelse($upcyclers as $u)
            <tr>
                <td>{{ $u->fname }} {{ $u->lname }}</td>
                <td>{{ $u->email }}</td>
                <td>{{ $u->created_at->format('M d, Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="3">No upcyclers registered this month.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- Sold Items -->
    <h2>Sold Items</h2>
    <div class="summary-box">
        <strong>Total Items Sold:</strong> {{ $products->count() }}<br>
        <strong>Total Revenue:</strong> ₱{{ number_format($products->sum('price'), 2) }}
    </div>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Seller</th>
                <th>Category</th>
                <th>Price</th>
                <th>Sold At</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $p)
            <tr>
                <td>{{ $p->name }}</td>
                <td>{{ optional($p->user)->fname }} {{ optional($p->user)->lname }}</td>
                <td>{{ optional($p->category)->name }}</td>
                <td>₱{{ number_format($p->price, 2) }}</td>
                <td>{{ $p->created_at->format('M d, Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="5">No sold products this month.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- Listed Items -->
    <h2>Listed Items (Active/Pending)</h2>
    <div class="summary-box">
        <strong>Total Listed Items:</strong> {{ $listedItems->count() }}<br>
        <strong>Active:</strong> {{ $listedItems->where('status', 'active')->count() }}<br>
        <strong>Pending:</strong> {{ $listedItems->where('status', 'pending')->count() }}
    </div>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Seller</th>
                <th>Category</th>
                <th>Price</th>
                <th>Status</th>
                <th>Listed At</th>
            </tr>
        </thead>
        <tbody>
            @forelse($listedItems as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td>{{ optional($item->user)->fname }} {{ optional($item->user)->lname }}</td>
                <td>{{ optional($item->category)->name }}</td>
                <td>₱{{ number_format($item->price, 2) }}</td>
                <td>{{ ucfirst($item->status) }}</td>
                <td>{{ $item->created_at->format('M d, Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="6">No listed items this month.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- Upcycled Works -->
    <h2>Upcycled Works</h2>
    <div class="summary-box">
        <strong>Total Works:</strong> {{ $works->count() }}
    </div>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Upcycler</th>
                <th>Upcycle Type</th>
                <th>Approval Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @forelse($works as $work)
            <tr>
                <td>{{ $work->title }}</td>
                <td>{{ optional($work->user)->fname }} {{ optional($work->user)->lname }}</td>
                <td>{{ $work->upcycle_type ?? 'N/A' }}</td>
                <td>{{ ucfirst($work->approval_status ?? 'pending') }}</td>
                <td>{{ $work->created_at->format('M d, Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="5">No upcycled works this month.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- Reported Users -->
    <h2>Reported Users</h2>
    <div class="summary-box">
        <strong>Total Reports:</strong> {{ $reportedUsers->count() }}<br>
        <strong>Pending:</strong> {{ $reportedUsers->where('status', 'pending')->count() }}<br>
        <strong>Resolved:</strong> {{ $reportedUsers->where('status', 'resolved')->count() }}<br>
        <strong>Rejected:</strong> {{ $reportedUsers->where('status', 'rejected')->count() }}
    </div>
    <table>
        <thead>
            <tr>
                <th>Reporter</th>
                <th>Reported User</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Reported At</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportedUsers as $report)
            <tr>
                <td>{{ optional($report->reporter)->fname }} {{ optional($report->reporter)->lname }}</td>
                <td>{{ optional($report->reportedUser)->fname }} {{ optional($report->reportedUser)->lname }}</td>
                <td>{{ $report->reason ?? 'N/A' }}</td>
                <td>{{ ucfirst($report->status ?? 'pending') }}</td>
                <td>{{ $report->created_at->format('M d, Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="5">No reports this month.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- Donations -->
    <h2>Donations</h2>
    <div class="summary-box">
        <strong>Total Donations:</strong> {{ $donations->count() }}<br>
        <strong>Approved:</strong> {{ $donations->where('approval_status', 'approved')->count() }}<br>
        <strong>Pending:</strong> {{ $donations->where('approval_status', 'pending')->count() }}<br>
        <strong>Rejected:</strong> {{ $donations->where('approval_status', 'rejected')->count() }}
    </div>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Donor</th>
                <th>Category</th>
                <th>Approval Status</th>
                <th>Verification Status</th>
                <th>Donated At</th>
            </tr>
        </thead>
        <tbody>
            @forelse($donations as $donation)
            <tr>
                <td>{{ $donation->name }}</td>
                <td>{{ optional($donation->user)->fname }} {{ optional($donation->user)->lname }}</td>
                <td>{{ optional($donation->category)->name }}</td>
                <td>{{ ucfirst($donation->approval_status ?? 'pending') }}</td>
                <td>{{ ucfirst($donation->verification_status ?? 'pending') }}</td>
                <td>{{ $donation->created_at->format('M d, Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="6">No donations this month.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

