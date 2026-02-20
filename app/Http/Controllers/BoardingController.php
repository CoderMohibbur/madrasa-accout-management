<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\AddAcademy;
use App\Models\AddClass;
use App\Models\AddSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class BoardingController extends Controller
{
    /**
     * Dropdown data: active + not deleted (Phase 2 style)
     */
    private function activeOnly(string $modelClass)
    {
        $m = new $modelClass();
        $table = $m->getTable();

        $q = $modelClass::query();

        if (Schema::hasColumn($table, 'isDeleted')) {
            $q->where('isDeleted', false);
        }
        if (Schema::hasColumn($table, 'isActived')) {
            $q->where('isActived', true);
        }

        return $q;
    }

    public function index(Request $request)
    {
        // Filters
        $yearId    = $request->input('academic_year_id');
        $classId   = $request->input('class_id');
        $sectionId = $request->input('section_id');
        $search    = trim((string) $request->input('search', ''));

        // Base query: only active + not deleted + boarding=true
        $q = Student::query()
            ->where('isDeleted', 0)
            ->where('isActived', 1)
            ->where('is_boarding', 1)
            ->with(['class', 'section', 'academicYear']);

        // Apply academic filters
        if ($yearId)    $q->where('academic_year_id', $yearId);
        if ($classId)   $q->where('class_id', $classId);
        if ($sectionId) $q->where('section_id', $sectionId);

        // Search: name/mobile/roll
        if ($search !== '') {
            $q->where(function ($qq) use ($search) {
                $qq->where('full_name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");

                if (ctype_digit($search)) {
                    $qq->orWhere('roll', (int) $search)
                       ->orWhere('id', (int) $search);
                }
            });
        }

        $students = $q->orderBy('class_id')
            ->orderBy('section_id')
            ->orderByRaw('CAST(roll as UNSIGNED) asc')
            ->paginate(30)
            ->withQueryString();

        // Dropdowns
        $years    = $this->activeOnly(AddAcademy::class)->orderByDesc('id')->get();
        $classes  = $this->activeOnly(AddClass::class)->orderBy('id')->get();
        $sections = $this->activeOnly(AddSection::class)->orderBy('id')->get();

        // Useful links (safe)
        $txCenterUrl = null;
        if (\Illuminate\Support\Facades\Route::has('transactions.center')) {
            $txCenterUrl = route('transactions.center');
        } elseif (\Illuminate\Support\Facades\Route::has('transaction-center.index')) {
            $txCenterUrl = route('transaction-center.index');
        } else {
            $txCenterUrl = url('/transaction-center');
        }

        return view('boarding.students.index', compact(
            'students',
            'years',
            'classes',
            'sections',
            'txCenterUrl'
        ));
    }
}