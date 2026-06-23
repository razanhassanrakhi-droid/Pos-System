@extends('admin.layouts.app')

@section('title', __('massage.contacts_management') ?? 'Manage Contacts')
@section('header', __('massage.contacts_management') ?? 'Manage Contacts')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div></div>
    
    <a href="{{ route('admin.contacts.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center gap-2 transition-colors">
        <i class="fas fa-plus"></i>
        <span>{{ __('massage.dt_add_contact') }}</span>
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse" id="dataTable">
            <thead class="bg-gray-50 text-gray-600 text-sm uppercase">
                <tr>
                    <th class="py-4 px-6 font-semibold">{{ __('massage.dt_phone_number') }}</th>
                    <th class="py-4 px-6 font-semibold">{{ __('massage.dt_social_links') }}</th>
                    <th class="py-4 px-6 font-semibold text-center">{{ __('massage.dt_actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700 text-sm">
                @forelse($contacts as $contact)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="py-4 px-6 font-medium text-gray-900">
                        <i class="fas fa-phone-alt text-gray-400 mr-2"></i> <span dir="ltr">{{ $contact->phone_number }}</span>
                    </td>
                    <td class="py-4 px-6 space-y-2">
                        @if($contact->facebook_url)
                            <a href="{{ $contact->facebook_url }}" target="_blank" class="flex items-center gap-2 text-blue-600 hover:underline">
                                <i class="fab fa-facebook"></i> Facebook
                            </a>
                        @endif
                        @if($contact->tiktok_url)
                            <a href="{{ $contact->tiktok_url }}" target="_blank" class="flex items-center gap-2 text-black hover:underline">
                                <i class="fab fa-tiktok"></i> TikTok
                            </a>
                        @endif
                        @if(!$contact->facebook_url && !$contact->tiktok_url)
                            <span class="text-gray-400"><i class="fas fa-minus"></i> No social links</span>
                        @endif
                    </td>
                    <td class="py-4 px-6 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.contacts.edit', $contact->id) }}" class="w-8 h-8 rounded bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-colors" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.contacts.destroy', $contact->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete these contact details?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded bg-red-50 text-red-600 hover:bg-red-600 hover:text-white flex items-center justify-center transition-colors" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="py-8 px-6 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center gap-3">
                            <i class="far fa-address-book text-4xl text-gray-300"></i>
                            <p>{{ __('massage.dt_no_contacts') }}</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
