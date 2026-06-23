@extends('admin.layouts.app')

@section('title', __('massage.users_management') ?? 'System Users')
@section('header', __('massage.users_management') ?? 'System Users')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div class="relative w-72">
        <div class="absolute inset-y-0 {{ app()->getLocale() == 'ar' ? 'right-0 pr-3' : 'left-0 pl-3' }} flex items-center pointer-events-none">
            <i class="fas fa-search text-gray-400"></i>
        </div>
        <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="{{ __('massage.dt_search_users') ?? 'Search users...' }}" class="w-full {{ app()->getLocale() == 'ar' ? 'pr-10 text-right' : 'pl-10 text-left' }} py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    
    <a href="{{ route('admin.users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center gap-2 transition-colors">
        <i class="fas fa-plus"></i>
        <span>{{ __('massage.dt_add_user') ?? 'Add User' }}</span>
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse" id="dataTable" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
            <thead class="bg-gray-50 text-gray-600 text-sm uppercase">
                <tr>
                    <th class="py-4 px-6 font-semibold {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('massage.dt_name') ?? 'Name' }}</th>
                    <th class="py-4 px-6 font-semibold {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('massage.dt_email') === 'massage.dt_email' ? 'البريد الإلكتروني' : __('massage.dt_email') }}</th>
                    <th class="py-4 px-6 font-semibold text-center">{{ __('massage.user_role') === 'massage.user_role' ? 'صنف المستخدم' : __('massage.user_role') }}</th>
                    <th class="py-4 px-6 font-semibold text-center">{{ __('massage.password') === 'massage.password' ? 'كلمة المرور' : __('massage.password') }}</th>
                    <th class="py-4 px-6 font-semibold hidden md:table-cell {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('massage.dt_date') ?? 'Date Added' }}</th>
                    <th class="py-4 px-6 font-semibold text-center">{{ __('massage.dt_actions') ?? 'Actions' }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700 text-sm">
                @forelse($users as $item)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="py-4 px-6">
                        <div class="font-medium text-gray-900 flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold uppercase">
                                {{ substr($item->name, 0, 1) }}
                            </div>
                            {{ $item->name }}
                        </div>
                    </td>
                    <td class="py-4 px-6" dir="ltr"><span class="{{ app()->getLocale() == 'ar' ? 'float-right' : '' }}">{{ $item->email }}</span></td>
                    <td class="py-4 px-6 text-center">
                        @if(auth()->user()->isAdmin() && auth()->id() !== $item->id)
                            <form action="{{ route('admin.users.update_role', $item->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <select name="role" onchange="this.form.submit()" class="p-1 rounded text-sm border-gray-300 focus:ring-blue-500 font-medium {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }} {{ $item->isAdmin() ? 'bg-purple-50 text-purple-800 border-purple-200' : 'bg-green-50 text-green-800 border-green-200' }}">
                                    <option value="admin" {{ $item->isAdmin() ? 'selected' : '' }}>{{ __('massage.role_admin') ?? 'Admin' }}</option>
                                    <option value="employee" {{ $item->isEmployee() ? 'selected' : '' }}>{{ __('massage.role_employee') ?? 'Employee' }}</option>
                                </select>
                            </form>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->isAdmin() ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' }}">
                                {{ $item->isAdmin() ? __('massage.role_admin') ?? 'Admin' : __('massage.role_employee') ?? 'Employee' }}
                            </span>
                        @endif
                    </td>
                    <td class="py-4 px-6 text-center">
                        <div class="flex items-center justify-center gap-2 text-gray-400">
                            <span class="font-mono tracking-widest mt-1" id="pw-mask-{{ $item->id }}">********</span>
                            <span class="font-mono mt-1 text-gray-800 hidden" id="pw-text-{{ $item->id }}">{{ $item->original_password ?? 'N/A' }}</span>
                            <button type="button" onclick="toggleTablePassword({{ $item->id }})" class="hover:text-blue-600 transition-colors" title="{{ __('massage.show_password') ?? 'Show Password' }}">
                                <i class="fas fa-eye" id="pw-icon-{{ $item->id }}"></i>
                            </button>
                        </div>
                    </td>
                    <td class="py-4 px-6 hidden md:table-cell text-gray-500">{{ $item->created_at->format('Y-m-d') }}</td>
                    <td class="py-4 px-6 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.users.edit', $item->id) }}" class="w-8 h-8 rounded bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-colors" title="{{ __('massage.edit') ?? 'Edit' }}">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if(auth()->id() !== $item->id)
                            <form action="{{ route('admin.users.destroy', $item->id) }}" method="POST" onsubmit="return confirm('{{ __('massage.confirm_delete') ?? 'Are you sure you want to delete this user?' }}');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded bg-red-50 text-red-600 hover:bg-red-600 hover:text-white flex items-center justify-center transition-colors" title="{{ __('massage.delete') ?? 'Delete' }}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                            @else
                            <button type="button" class="w-8 h-8 rounded bg-gray-100 text-gray-400 cursor-not-allowed flex items-center justify-center" title="Cannot delete yourself">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 px-6 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center gap-3">
                            <i class="fas fa-users-slash text-4xl text-gray-300"></i>
                            <p>{{ __('massage.dt_no_users') ?? 'No users found.' }}</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
function searchTable() {
    const filter = document.getElementById("searchInput").value.toLowerCase();
    const rows = document.querySelectorAll("#dataTable tbody tr");

    rows.forEach(row => {
        if(row.cells.length === 1) return;
        const rowText = Array.from(row.cells).map(cell => cell.innerText.toLowerCase()).join(" ");
        row.style.display = rowText.includes(filter) ? "" : "none";
    });
}

function toggleTablePassword(id) {
    const mask = document.getElementById('pw-mask-' + id);
    const text = document.getElementById('pw-text-' + id);
    const icon = document.getElementById('pw-icon-' + id);
    
    if (text.classList.contains('hidden')) {
        text.classList.remove('hidden');
        mask.classList.add('hidden');
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        text.classList.add('hidden');
        mask.classList.remove('hidden');
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endpush
