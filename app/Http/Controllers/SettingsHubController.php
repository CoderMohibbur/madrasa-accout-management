<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SettingsHubController extends Controller
{
    private function entities(): array
    {
        // আপনার টেবিল নাম অনুযায়ী map
        return [
            'classes' => [
                'title' => 'Classes',
                'table' => 'add_classes',
                'pk'    => 'id',
                'fields' => [
                    ['name' => 'name', 'label' => 'Class Name', 'type' => 'text', 'rules' => 'required|string|max:120'],
                ],
                'orderBy' => 'name',
            ],

            'months' => [
                'title' => 'Months',
                'table' => 'add_months',
                'pk'    => 'id',
                'fields' => [
                    ['name' => 'name', 'label' => 'Month Name', 'type' => 'text', 'rules' => 'required|string|max:50'],
                ],
                'orderBy' => 'id',
            ],

            'academies' => [
                'title' => 'Academic Year',
                'table' => 'add_academies',
                'pk'    => 'id',
                'fields' => [
                    ['name' => 'year', 'label' => 'Year', 'type' => 'text', 'rules' => 'required|string|max:20'],
                    ['name' => 'academic_years', 'label' => 'Title', 'type' => 'text', 'rules' => 'nullable|string|max:100'],
                    ['name' => 'starting_date', 'label' => 'Start Date', 'type' => 'date', 'rules' => 'required|date'],
                    ['name' => 'ending_date', 'label' => 'End Date', 'type' => 'date', 'rules' => 'required|date|after_or_equal:starting_date'],
                ],
                'orderBy' => 'starting_date',
            ],

            'fees_types' => [
                'title' => 'Fees Types',
                'table' => 'add_fess_types', // আপনার টেবিল নাম যদি ভিন্ন হয় এখানে ঠিক করুন
                'pk'    => 'id',
                'fields' => [
                    ['name' => 'name', 'label' => 'Fees Type', 'type' => 'text', 'rules' => 'required|string|max:120'],
                    ['name' => 'amount', 'label' => 'Amount', 'type' => 'number', 'rules' => 'nullable|numeric|min:0'],
                ],
                'orderBy' => 'name',
            ],

            'transactions_types' => [
                'title' => 'Transactions Types',
                'table' => 'transactions_types',
                'pk'    => 'id',
                'fields' => [
                    ['name' => 'name', 'label' => 'Type Name', 'type' => 'text', 'rules' => 'required|string|max:120'],
                    ['name' => 'key', 'label' => 'Key (optional)', 'type' => 'text', 'rules' => 'nullable|string|max:120'],
                ],
                'orderBy' => 'name',
            ],

            'sections' => [
                'title' => 'Sections',
                'table' => 'add_sections',
                'pk'    => 'id',
                'fields' => [
                    ['name' => 'name', 'label' => 'Section Name', 'type' => 'text', 'rules' => 'required|string|max:120'],
                ],
                'orderBy' => 'name',
            ],

            'registration_fees' => [
                'title' => 'Registration Fees',
                'table' => 'add_registration_fess', // আপনার টেবিল নাম যদি ভিন্ন হয় এখানে ঠিক করুন
                'pk'    => 'id',
                'fields' => [
                    ['name' => 'name', 'label' => 'Title', 'type' => 'text', 'rules' => 'required|string|max:120'],
                    ['name' => 'amount', 'label' => 'Amount', 'type' => 'number', 'rules' => 'required|numeric|min:0'],
                ],
                'orderBy' => 'id',
            ],
        ];
    }

    private function metaOrFail(string $entity): array
    {
        $meta = $this->entities()[$entity] ?? null;
        abort_unless($meta, 404);

        abort_unless(Schema::hasTable($meta['table']), 500, "Missing table: {$meta['table']}");

        return $meta;
    }

    private function baseQuery(array $meta)
    {
        $q = DB::table($meta['table']);

        if (Schema::hasColumn($meta['table'], 'isDeleted')) {
            $q->where('isDeleted', false);
        }

        return $q;
    }

    public function index()
    {
        $entities = $this->entities();
        $data = [];

        foreach ($entities as $key => $meta) {
            if (!Schema::hasTable($meta['table'])) {
                $data[$key] = collect();
                continue;
            }

            $q = $this->baseQuery($meta);

            if (!empty($meta['orderBy']) && Schema::hasColumn($meta['table'], $meta['orderBy'])) {
                $q->orderBy($meta['orderBy']);
            } else {
                $q->orderBy($meta['pk']);
            }

            $data[$key] = $q->limit(200)->get();
        }

        return view('settings.index', compact('entities', 'data'));
    }

    public function store(Request $request, string $entity)
    {
        $meta = $this->metaOrFail($entity);

        $rules = [];
        foreach ($meta['fields'] as $f) $rules[$f['name']] = $f['rules'] ?? 'nullable';

        $validated = $request->validate($rules);

        if (Schema::hasColumn($meta['table'], 'isActived') && !array_key_exists('isActived', $validated)) {
            $validated['isActived'] = true;
        }
        if (Schema::hasColumn($meta['table'], 'isDeleted') && !array_key_exists('isDeleted', $validated)) {
            $validated['isDeleted'] = false;
        }

        $validated['created_at'] = now();
        $validated['updated_at'] = now();

        DB::table($meta['table'])->insert($validated);

        return back()->with('success', "{$meta['title']} added");
    }

    public function update(Request $request, string $entity, int $id)
    {
        $meta = $this->metaOrFail($entity);

        $rules = [];
        foreach ($meta['fields'] as $f) $rules[$f['name']] = $f['rules'] ?? 'nullable';

        $validated = $request->validate($rules);
        $validated['updated_at'] = now();

        $this->baseQuery($meta)->where($meta['pk'], $id)->update($validated);

        return back()->with('success', "{$meta['title']} updated");
    }

    public function toggle(string $entity, int $id)
    {
        $meta = $this->metaOrFail($entity);

        if (!Schema::hasColumn($meta['table'], 'isActived')) {
            return back()->with('success', "No isActived column in {$meta['table']}");
        }

        $row = $this->baseQuery($meta)->where($meta['pk'], $id)->first();
        if (!$row) return back();

        DB::table($meta['table'])
            ->where($meta['pk'], $id)
            ->update(['isActived' => !(bool)$row->isActived, 'updated_at' => now()]);

        return back()->with('success', "{$meta['title']} status changed");
    }

    public function destroy(string $entity, int $id)
    {
        $meta = $this->metaOrFail($entity);

        // Soft delete preference: isDeleted থাকলে সেটাই দিন
        if (Schema::hasColumn($meta['table'], 'isDeleted')) {
            DB::table($meta['table'])->where($meta['pk'], $id)->update([
                'isDeleted' => true,
                'updated_at' => now(),
            ]);
        } else {
            DB::table($meta['table'])->where($meta['pk'], $id)->delete();
        }

        return back()->with('success', "{$meta['title']} removed");
    }
}
