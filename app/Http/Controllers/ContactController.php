<?php

namespace App\Http\Controllers;

use App\Models\Contacts;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    // عرض كل جهات الاتصال
    public function index()
    {
        $contacts = Contacts::all();
        return view('admin.contacts.index', compact('contacts'));
    }

    public function create()
    {
        return view('admin.contacts.create');
    }

    // حفظ جهة اتصال جديدة
    public function store(Request $request)
    {
        $data = $request->validate([
            'phone_number' => 'required|string|max:20',
            'facebook_url' => 'nullable|url|max:255',
            'tiktok_url'   => 'nullable|url|max:255',
        ]);

        Contacts::create($data);

        return redirect()->route('admin.contacts.index')
                         ->with('success', 'تم إضافة جهة الاتصال بنجاح!');
    }

    // عرض صفحة تعديل جهة اتصال موجودة
    public function edit($id)
    {
        $contact = Contacts::findOrFail($id);
        return view('admin.contacts.edit', compact('contact'));
    }

    // تحديث جهة الاتصال
    public function update(Request $request, $id)
    {
        $contact = Contacts::findOrFail($id);

        $data = $request->validate([
            'phone_number' => 'required|string|max:20',
            'facebook_url' => 'nullable|url|max:255',
            'tiktok_url'   => 'nullable|url|max:255',
        ]);

        $contact->update($data);

        return redirect()->route('admin.contacts.index')
                         ->with('success', 'تم تحديث جهة الاتصال بنجاح!');
    }

    // حذف جهة الاتصال
    public function destroy($id)
    {
        $contacts = Contacts::findOrFail($id);
        $contacts->delete();

        return redirect()->route('admin.contacts.index')
                         ->with('success', 'تم حذف جهة الاتصال بنجاح!');
    }
}
