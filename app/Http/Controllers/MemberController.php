<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Models\Member;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index()
    {
        $members = Member::when(request('search'), fn ($query, $search) => 
            $query->where('nama', 'like', "%{$search}%")
        )->paginate(10);

        return view('members.index', compact('members'));
    }

    public function create()
    {
        return view('members.create');
    }

    public function store(StoreMemberRequest $request)
    {
        $validated = $request->validated();

        try {
            Member::create($validated);

            return redirect()->route('members.index')
                ->with('success', "Anggota \"{$validated['nama']}\" berhasil ditambahkan.");
        } catch (UniqueConstraintViolationException $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'nim' => 'NIM atau Email ini sudah terdaftar di database.',
                ]);
        }
    }

    public function show(string $id)
    {
        $member = Member::findOrFail($id);

        return view('members.show', compact('member'));
    }

    public function edit(string $id)
    {
        $member = Member::findOrFail($id);

        return view('members.edit', compact('member'));
    }

    public function update(Request $request, string $id)
    {
        $member = Member::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'nim' => "required|string|max:20|unique:members,nim,{$id}",
            'email' => "required|email|max:100|unique:members,email,{$id}",
            'nomor_telepon' => 'required|string|max:15',
            'alamat' => 'required|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        try {
            $member->update($validated);

            return redirect()->route('members.index')
                ->with('success', "Anggota \"{$validated['nama']}\" berhasil diperbarui.");
        } catch (UniqueConstraintViolationException $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'nim' => 'NIM atau Email sudah dipakai oleh anggota lain.',
                ]);
        }
    }

    public function destroy(string $id)
    {
        $member = Member::findOrFail($id);
        $member->delete();

        return redirect()->route('members.index')
            ->with('success', 'Anggota berhasil dihapus.');
    }
}