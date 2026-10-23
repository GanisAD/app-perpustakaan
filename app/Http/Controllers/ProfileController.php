<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show ()
    {
        $user = Auth::user();

        return view('profile.show', compact('user'));
    }

    public function updatePassword (Request $request)
    {
        $request -> validate([
            'current_password' => ['required', function ($attribute, $value, $fail) {
                if (! Hash::check($value, Auth::user()->password))
                {
                    $fail('Password lama tidak sesuai');
                }
            }],
            'password' => 'required|string|min:8|confirmed'
        ], [        
            'current_password.required' => 'Password Lama wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user = Auth::user();
        $user -> update([
            'password' => Hash::make($request->password),
        ]);
        
        return back()->with('success', 'Password berhasil diubah.');
    }
}
