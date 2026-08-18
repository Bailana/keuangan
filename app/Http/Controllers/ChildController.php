<?php

namespace App\Http\Controllers;

use App\Exports\ChildExport;
use App\Models\Child;
use App\Models\InvoicePayment;
use App\Models\TherapyType;
use App\Models\VocationalType;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ChildController extends Controller
{
    public function index()
    {
        $query = Child::with(['therapyTypes', 'vocationalTypes']);

        if (request()->filled('search')) {
            $query->where('name', 'like', '%' . request('search') . '%');
        }

        if (request()->filled('category')) {
            $query->whereHas('plans', function ($q) {
                $q->where('category', request('category'));
            });
        }

        if (request()->filled('parent_phone')) {
            $query->where('parent_whatsapp', 'like', '%' . request('parent_phone') . '%');
        }

        $allChildren = Child::with(['therapyTypes', 'vocationalTypes'])->get();

        $totalTerapi = 0;
        $totalVokasi = 0;
        $totalSekolah = 0;
        $totalSubsidi = 0;
        $totalInvoice = 0;

        foreach ($allChildren as $child) {
            $subsidi = $child->getSubsidiAmount();
            $totalSubsidi += $subsidi;

            foreach ($child->therapyTypes as $t) {
                $sessions = $t->pivot->monthly_sessions ?? 0;
                $totalTerapi += (float) $t->price_per_session * (int) $sessions;
            }
            foreach ($child->vocationalTypes as $v) {
                $sessions = $v->pivot->monthly_sessions ?? 0;
                $totalVokasi += (float) $v->price_per_session * (int) $sessions;
            }
            if ($child->isTakingSekolah()) {
                $schoolFee = $child->spp_fee ?? config('settings.school_fee', 0);
                $totalSekolah += (float) $schoolFee;
            }

            // Calculate invoice for current month
            $invoiceAmount = $child->calculateInvoiceAmount(now()->month, now()->year);
            $totalInvoice += $invoiceAmount;
        }

        $children = $query->latest()->paginate(6)->withQueryString();

        $therapyTypes = TherapyType::all();
        $vocationalTypes = VocationalType::all();

        $parentSupportFee = config('settings.parent_support_fee', 25000);
        $totalParentSupport = 0;
        foreach ($allChildren as $child) {
            if ($child->has_parent_support) {
                $totalParentSupport += (float) $parentSupportFee;
            }
        }

        return view('children.index', compact(
            'children', 'therapyTypes', 'vocationalTypes',
            'totalTerapi', 'totalVokasi', 'totalSekolah', 'totalSubsidi', 'totalInvoice', 'totalParentSupport'
        ));
    }

    public function create()
    {
        $therapyTypes = TherapyType::all();
        $vocationalTypes = VocationalType::all();
        return view('children.create', compact('therapyTypes', 'vocationalTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_name' => 'nullable|string|max:255',
            'parent_whatsapp' => 'nullable|string|max:20',
            'class_name' => 'nullable|string|max:255',
            'spp_fee' => 'nullable|numeric|min:0',
            'has_subsidi' => 'nullable|boolean',
            'subsidi_amount' => 'nullable|numeric|min:0',
            'has_parent_support' => 'nullable|boolean',
            'therapy_types' => 'nullable|array',
            'therapy_sessions' => 'nullable|array',
            'vocational_types' => 'nullable|array',
            'vocational_sessions' => 'nullable|array',
        ]);

        // If school service is not selected, clear school-related fields
        if (!$request->has('is_sekolah')) {
            $request->merge(['class_name' => null, 'spp_fee' => null]);
        }

        $child = Child::create([
            'name' => $validated['name'],
            'parent_name' => $validated['parent_name'] ?? null,
            'parent_whatsapp' => $validated['parent_whatsapp'] ?? null,
            'class_name' => $request->class_name,
            'spp_fee' => $request->spp_fee,
            'has_subsidi' => $validated['has_subsidi'] ?? false,
            'subsidi_amount' => ($validated['has_subsidi'] ?? false) ? ($validated['subsidi_amount'] ?? 0) : null,
            'has_parent_support' => $validated['has_parent_support'] ?? false,
        ]);

        // Attach therapy types
        if (!empty($validated['therapy_types'])) {
            foreach ($validated['therapy_types'] as $therapyId) {
                $sessions = $validated['therapy_sessions'][$therapyId] ?? 4;
                $child->therapyTypes()->attach($therapyId, ['monthly_sessions' => (int) $sessions]);
            }
        }

        // Attach vocational types
        if (!empty($validated['vocational_types'])) {
            foreach ($validated['vocational_types'] as $vocationalId) {
                $sessions = $validated['vocational_sessions'][$vocationalId] ?? 4;
                $child->vocationalTypes()->attach($vocationalId, ['monthly_sessions' => (int) $sessions]);
            }
        }

        return redirect()->route('children.index', request()->query())->with('success', 'Data anak berhasil ditambahkan.');
    }

    public function toggleActive(Request $request, Child $child)
    {
        $child->update(['is_active' => !$child->is_active]);
        return redirect()->route('children.index', $request->query())->with('success', $child->is_active ? 'Anak diaktifkan.' : 'Anak dinonaktifkan.');
    }

    public function edit(Child $child)
    {
        $therapyTypes = TherapyType::all();
        $vocationalTypes = VocationalType::all();
        return view('children.edit', compact('child', 'therapyTypes', 'vocationalTypes'));
    }

    public function update(Request $request, Child $child)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_name' => 'nullable|string|max:255',
            'parent_whatsapp' => 'nullable|string|max:20',
            'class_name' => 'nullable|string|max:255',
            'spp_fee' => 'nullable|numeric|min:0',
            'has_subsidi' => 'nullable|boolean',
            'subsidi_amount' => 'nullable|numeric|min:0',
            'has_parent_support' => 'nullable|boolean',
            'therapy_types' => 'nullable|array',
            'therapy_sessions' => 'nullable|array',
            'vocational_types' => 'nullable|array',
            'vocational_sessions' => 'nullable|array',
        ]);

        // If school service is not selected, clear school-related fields
        if (!$request->has('is_sekolah')) {
            $request->merge(['class_name' => null, 'spp_fee' => null]);
        }

        $child->update([
            'name' => $validated['name'],
            'parent_name' => $validated['parent_name'] ?? null,
            'parent_whatsapp' => $validated['parent_whatsapp'] ?? null,
            'class_name' => $request->class_name,
            'spp_fee' => $request->spp_fee,
            'has_subsidi' => $validated['has_subsidi'] ?? false,
            'subsidi_amount' => ($validated['has_subsidi'] ?? false) ? ($validated['subsidi_amount'] ?? 0) : null,
            'has_parent_support' => $validated['has_parent_support'] ?? false,
        ]);

        // Sync therapy types
        if (!empty($validated['therapy_types'])) {
            $therapyData = [];
            foreach ($validated['therapy_types'] as $therapyId) {
                $sessions = $validated['therapy_sessions'][$therapyId] ?? 4;
                $therapyData[$therapyId] = ['monthly_sessions' => (int) $sessions];
            }
            $child->therapyTypes()->sync($therapyData);
        } else {
            $child->therapyTypes()->detach();
        }

        // Sync vocational types
        if (!empty($validated['vocational_types'])) {
            $vocationalData = [];
            foreach ($validated['vocational_types'] as $vocationalId) {
                $sessions = $validated['vocational_sessions'][$vocationalId] ?? 4;
                $vocationalData[$vocationalId] = ['monthly_sessions' => (int) $sessions];
            }
            $child->vocationalTypes()->sync($vocationalData);
        } else {
            $child->vocationalTypes()->detach();
        }

        // Regenerate invoice amount for current month
        InvoicePayment::generateForChild($child->id, now()->month, now()->year);

        return redirect()->route('children.index', $request->query())->with('success', 'Data anak berhasil diperbarui.');
    }

    public function destroy(Child $child)
    {
        $child->delete();
        return redirect()->route('children.index', request()->query())->with('success', 'Data anak berhasil dihapus.');
    }

    public function exportPdf()
    {
        $query = Child::with(['therapyTypes', 'vocationalTypes']);

        if (request()->filled('search')) {
            $query->where('name', 'like', '%' . request('search') . '%');
        }
        if (request()->filled('category')) {
            $query->whereHas('plans', function ($q) {
                $q->where('category', request('category'));
            });
        }
        if (request()->filled('parent_phone')) {
            $query->where('parent_whatsapp', 'like', '%' . request('parent_phone') . '%');
        }

        $children = $query->get();

        $totalTerapi = 0;
        $totalVokasi = 0;
        $totalSekolah = 0;
        $totalSubsidi = 0;
        $totalInvoice = 0;

        foreach ($children as $child) {
            foreach ($child->therapyTypes as $t) {
                $sessions = $t->pivot->monthly_sessions ?? 0;
                $totalTerapi += (float) $t->price_per_session * (int) $sessions;
            }
            foreach ($child->vocationalTypes as $v) {
                $sessions = $v->pivot->monthly_sessions ?? 0;
                $totalVokasi += (float) $v->price_per_session * (int) $sessions;
            }
            if ($child->isTakingSekolah()) {
                $schoolFee = $child->spp_fee ?? config('settings.school_fee', 0);
                $totalSekolah += (float) $schoolFee;
            }
            $totalSubsidi += $child->getSubsidiAmount();
            $totalInvoice += $child->calculateInvoiceAmount(now()->month, now()->year);
        }

        $filters = [];
        if (request('search')) $filters['Pencarian'] = request('search');
        if (request('category')) $filters['Kategori'] = request('category');
        if (request('parent_phone')) $filters['No. HP'] = request('parent_phone');

        $pdf = Pdf::loadView('children.pdf-export', [
            'children' => $children,
            'totalTerapi' => $totalTerapi,
            'totalVokasi' => $totalVokasi,
            'totalSekolah' => $totalSekolah,
            'totalSubsidi' => $totalSubsidi,
            'totalInvoice' => $totalInvoice,
            'filters' => $filters,
            'generatedDate' => now()->format('d-m-Y'),
        ]);

        return $pdf->download('Laporan-Data-Anak-' . now()->format('Ymd-His') . '.pdf');
    }

    public function exportExcel()
    {
        $query = Child::with(['therapyTypes', 'vocationalTypes']);

        if (request()->filled('search')) {
            $query->where('name', 'like', '%' . request('search') . '%');
        }
        if (request()->filled('category')) {
            $query->whereHas('plans', function ($q) {
                $q->where('category', request('category'));
            });
        }
        if (request()->filled('parent_phone')) {
            $query->where('parent_whatsapp', 'like', '%' . request('parent_phone') . '%');
        }

        $children = $query->get();

        return Excel::download(new ChildExport($children), 'Laporan-Data-Anak-' . now()->format('Ymd-His') . '.xlsx');
    }
}
