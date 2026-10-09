<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    /**
     * Menampilkan daftar peminjaman dengan eager loading relasi
     * untuk mencegah N+1 Query Problem.
     */
    public function index()
    {
        $loans = Loan::with(['member', 'user', 'loanItems.book'])->paginate(10);

        return view('loans.index', compact('loans'));
    }

    /**
     * Menampilkan form tambah peminjaman dengan data anggota, buku, dan petugas.
     */
    public function create()
    {
        $members = Member::all();
        $books = Book::all();
        $users = User::all();

        return view('loans.create', compact('members', 'books', 'users'));
    }

    /**
     * Menyimpan transaksi peminjaman baru ke 2 tabel sekaligus (loans & loan_items).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|integer|exists:members,id',
            'user_id' => 'required|integer|exists:users,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'book_ids' => 'required|array|min:1',
            'book_ids.*' => 'integer|exists:books,id',
        ]);

        // 1. Simpan data transaksi utama ke tabel loans
        $loan = Loan::create([
            'member_id' => $validated['member_id'],
            'user_id' => $validated['user_id'],
            'tanggal_pinjam' => $validated['tanggal_pinjam'],
            'tanggal_kembali' => $validated['tanggal_kembali'],
        ]);

        // 2. Simpan setiap buku yang dipilih ke tabel loan_items lewat relasi
        foreach ($validated['book_ids'] as $bookId) {
            $loan->loanItems()->create(['book_id' => $bookId]);
        }

        return redirect()->route('loans.index')
            ->with('success', 'Transaksi peminjaman berhasil dibuat.');
    }

    /**
     * Menampilkan detail satu transaksi peminjaman.
     */
    public function show(string $id)
    {
        $loan = Loan::with(['member', 'user', 'loanItems.book'])->findOrFail($id);

        return view('loans.show', compact('loan'));
    }

    /**
     * Menampilkan form edit transaksi peminjaman.
     */
    public function edit(string $id)
    {
        $loan = Loan::with(['member', 'user', 'loanItems.book'])->findOrFail($id);

        return view('loans.edit', compact('loan'));
    }

    /**
     * Memperbarui tanggal kembali dan status peminjaman.
     */
    public function update(Request $request, string $id)
    {
        $loan = Loan::findOrFail($id);

        $validated = $request->validate([
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'status' => 'required|in:dipinjam,dikembalikan,terlambat',
        ], [
            'tanggal_kembali.required' => 'Tanggal kembali wajib diisi.',
            'tanggal_kembali.after_or_equal' => 'Tanggal kembali tidak boleh sebelum tanggal pinjam.',
        ]);

        $loan->update($validated);

        return redirect()->route('loans.index')
            ->with('success', 'Transaksi peminjaman berhasil diperbarui.');
    }

    /**
     * Menghapus transaksi peminjaman beserta seluruh item bukunya.
     */
    public function destroy(string $id)
    {
        $loan = Loan::findOrFail($id);
        
        // Hapus item-itemnya terlebih dahulu untuk menghindari error foreign key constraint
        $loan->loanItems()->delete();
        $loan->delete();

        return redirect()->route('loans.index')
            ->with('success', 'Transaksi peminjaman berhasil dihapus.');
    }

    /**
     * Fitur Tugas Mandiri: Mengubah status transaksi menjadi dikembalikan.
     */
    public function kembalikan(string $id)
    {
        $loan = Loan::findOrFail($id);

        $loan->update([
            'status' => 'dikembalikan',
            'tanggal_dikembalikan' => now()->toDateString(),
        ]);

        return redirect()->route('loans.index')
            ->with('success', 'Buku berhasil dikembalikan.');
    }
}