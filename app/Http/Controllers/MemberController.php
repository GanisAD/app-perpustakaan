<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MemberController extends Controller
{
    private array $members = [
        [
            'id' => 1,
            'nama' => 'Ahmad Dahlan',
            'nim' => '21010001',
            'email' => 'ahmad@example.com',
            'nomor_telepon' => '081234567890',
            'alamat' => 'Surabaya',
            'status' => 'aktif',
        ],
        [
            'id' => 2,
            'nama' => 'Siti Nurhaliza',
            'nim' => '21010002',
            'email' => 'siti@example.com',
            'nomor_telepon' => '081234567891',
            'alamat' => 'Sidoarjo',
            'status' => 'aktif',
        ],
        [
            'id' => 3,
            'nama' => 'Budi Santoso',
            'nim' => '21010003',
            'email' => 'budi@example.com',
            'nomor_telepon' => '081234567892',
            'alamat' => 'Gresik',
            'status' => 'nonaktif',
        ],
    ];

    public function index()
    {
        $members = $this->members;

        return view('members.index', compact('members'));
    }

    public function create()
    {
        return view('members.create');
    }

    public function store(StoreMemberRequest $request)
    {
        $validated = $request->validated();

        return redirect()->route('members.index')
            ->with('success', "Anggota \"{$validated['nama']}\" berhasil ditambahkan (data dummy, belum tersimpan ke database).");
    }

    public function show(string $id)
    {
        $member = collect($this->members)->firstWhere('id', (int) $id);

        abort_if(! $member, 404);

        return "MemberController@show, id: {$id}";
    }

    public function edit(string $id)
    {
        return "MemberController@edit, id: {$id}";
    }

    public function update(Request $request, string $id)
    {
        return "MemberController@update, id: {$id}";
    }

    public function destroy(string $id)
    {
        return redirect()->route('members.index')
            ->with('success', "Anggota dengan id {$id} berhasil dihapus (data dummy, belum tersimpan ke database).");
    }
}