<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    private array $members = [
        [
            'id' => 1,
            'nama' => 'Ahmad Dahlan',
            'nim' => '2024001',
            'email' => 'ahmad@example.com',
            'nomor_telepon' => '081234567890',
            'alamat' => 'Jl. Ketintang Baru No. 10',
            'status' => 'aktif'
        ],
        [
            'id' => 2,
            'nama' => 'Siti Nurhaliza',
            'nim' => '2024002',
            'email' => 'siti@example.com',
            'nomor_telepon' => '081298765432',
            'alamat' => 'Jl. Raya Manyar No. 25',
            'status' => 'aktif'
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
        return "MemberController@destroy, id: {$id}";
    }
}