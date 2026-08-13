<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pemasukan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-2xl mx-auto py-8 px-4">
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Pemasukan</h1>

            <form action="{{ route('incomes.update', $income) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori <span class="text-emerald-600">*</span></label>
                    <select name="income_category_id" required class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm bg-white">
                        <option value="">— Pilih kategori —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('income_category_id', $income->income_category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Anak <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <select name="child_id" class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm bg-white">
                        <option value="">— Umum / Tidak terkait anak —</option>
                        @foreach($children as $child)
                            <option value="{{ $child->id }}" {{ old('child_id', $income->child_id) == $child->id ? 'selected' : '' }}>{{ $child->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Pengirim</label>
                    <input type="text" name="sender_name" value="{{ old('sender_name', $income->sender_name) }}" class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm" placeholder="Nama pengirim / pembayar (opsional)">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Dompet <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <select name="wallet_id" class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm bg-white">
                        <option value="">— Pilih dompet —</option>
                        @foreach($wallets as $wallet)
                            <option value="{{ $wallet->id }}" {{ old('wallet_id', $income->wallet_id) == $wallet->id ? 'selected' : '' }}>{{ $wallet->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal <span class="text-emerald-600">*</span></label>
                        <input type="date" name="date" required value="{{ old('date', $income->date?->format('Y-m-d')) }}" class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah (Rp) <span class="text-emerald-600">*</span></label>
                        <input type="number" name="amount" required min="0" step="100" value="{{ old('amount', $income->amount) }}" class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan</label>
                    <textarea name="notes" rows="2" class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm" placeholder="Opsional...">{{ old('notes', $income->notes) }}</textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <a href="{{ url()->previous() }}" class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium">Batal</a>
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium shadow-lg shadow-emerald-500/20">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
