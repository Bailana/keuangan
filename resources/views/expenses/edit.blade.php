<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengeluaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-2xl mx-auto py-8 px-4">
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Pengeluaran</h1>

            <form action="{{ route('expenses.update', $expense) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Judul Pengeluaran <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $expense->title) }}" required class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 px-4 py-2.5 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                    <select name="expense_category_id" required class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 px-4 py-2.5 text-sm bg-white">
                        <option value="">— Pilih kategori —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('expense_category_id', $expense->expense_category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                        <input type="date" name="date" required value="{{ old('date', $expense->date?->format('Y-m-d')) }}" class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 px-4 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" required min="0" step="100" value="{{ old('amount', $expense->amount) }}" class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 px-4 py-2.5 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Sumber Dana (Dompet)</label>
                    <select name="wallet_id" class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 px-4 py-2.5 text-sm bg-white">
                        <option value="">— Pilih dompet —</option>
                        @foreach($wallets as $wallet)
                            <option value="{{ $wallet->id }}" {{ old('wallet_id', $expense->wallet_id) == $wallet->id ? 'selected' : '' }}>{{ $wallet->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Penerima</label>
                    <input type="text" name="recipient" value="{{ old('recipient', $expense->recipient) }}" class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 px-4 py-2.5 text-sm" placeholder="Contoh: Bp. Ahmad">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Upload Bukti <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <input type="file" name="receipt_url" accept="image/*,.pdf" class="w-full text-sm text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-red-50 file:text-red-700 hover:file:bg-red-100 rounded-xl border-gray-300">
                    @if($expense->receipt_url)
                        <p class="text-xs text-gray-500 mt-1">File saat ini: {{ basename($expense->receipt_url) }}</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan</label>
                    <textarea name="notes" rows="2" class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 px-4 py-2.5 text-sm" placeholder="Opsional...">{{ old('notes', $expense->notes) }}</textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <a href="{{ url()->previous() }}" class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium">Batal</a>
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-medium shadow-lg shadow-red-500/20">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
