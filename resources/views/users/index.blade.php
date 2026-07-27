@extends('layouts.admin')

@section('title', 'RBAC Role & Permission Management')
@section('page_title', 'Role-Based Access Control (RBAC) & POS PIN Security')

@section('content')
<div class="glass-card p-4">
    <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom border-secondary border-opacity-25">
        <div>
            <h5 class="mb-1 fw-bold font-heading"><i class="bi bi-shield-lock-fill text-danger me-2"></i> Complete Staff Role Governance</h5>
            <div class="text-secondary fs-7">Strict operational access separation between management, kitchen teams, tableside waitstaff, and financial controllers.</div>
        </div>
        <button class="btn btn-modern-primary btn-sm"><i class="bi bi-person-plus-fill me-2"></i> Provision Employee Account</button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 text-dark dark:text-white">
            <thead class="fs-7 text-secondary text-uppercase">
                <tr>
                    <th class="border-0 pb-3">Staff Member & Email</th>
                    <th class="border-0 pb-3">Assigned Role Profile</th>
                    <th class="border-0 pb-3">Fast POS Switch PIN</th>
                    <th class="border-0 pb-3">Phone Number</th>
                    <th class="border-0 pb-3">Account Status</th>
                    <th class="border-0 pb-3 text-end">Audit Controls</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold me-3" style="width:40px; height:40px;">
                                    {{ substr($user->name, 0, 2) }}
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold font-heading">{{ $user->name }}</h6>
                                    <span class="text-muted fs-7">{{ $user->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            @php
                                $badgeColor = match($user->role) {
                                    'superadmin', 'admin' => 'badge-emerald',
                                    'chef', 'kitchenmanager' => 'badge-crimson',
                                    'itadmin' => 'badge-amber',
                                    'accounts' => 'badge-azure',
                                    default => 'bg-secondary text-white'
                                };
                            @endphp
                            <span class="badge {{ $badgeColor }} fs-7 px-3 py-2">{{ strtoupper($user->role) }}</span>
                        </td>
                        <td>
                            <span class="badge bg-dark text-warning font-monospace fs-6 px-3"><i class="bi bi-key-fill me-1"></i> {{ $user->pin_code ?? 'None' }}</span>
                        </td>
                        <td class="font-monospace text-secondary">{{ $user->phone ?? 'Unlisted' }}</td>
                        <td><span class="badge badge-emerald"><i class="bi bi-check-circle me-1"></i> ACTIVE</span></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-light border rounded-3"><i class="bi bi-pencil-square me-1"></i> Permissions</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
