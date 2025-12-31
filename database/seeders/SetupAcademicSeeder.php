<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SetupAcademicSeeder extends Seeder
{
    public function run(): void
    {
        // ---------- add_academies ----------
        if (DB::table('add_academies')->count() == 0) {
            $rows = [];
            foreach (['2024', '2025', '2026'] as $y) {
                $row = [];
                if (Schema::hasColumn('add_academies', 'year')) $row['year'] = $y;
                if (Schema::hasColumn('add_academies', 'name')) $row['name'] = $y;
                if (Schema::hasColumn('add_academies', 'title')) $row['title'] = $y;

                if (Schema::hasColumn('add_academies', 'isActived')) $row['isActived'] = 1;
                if (Schema::hasColumn('add_academies', 'isDeleted')) $row['isDeleted'] = 0;
                if (Schema::hasColumn('add_academies', 'created_at')) $row['created_at'] = now();
                if (Schema::hasColumn('add_academies', 'updated_at')) $row['updated_at'] = now();

                $rows[] = $row;
            }
            DB::table('add_academies')->insert($rows);
        }

        // ---------- add_months ----------
        if (DB::table('add_months')->count() == 0) {
            $months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
            $rows = [];
            foreach ($months as $m) {
                $row = [];
                if (Schema::hasColumn('add_months', 'name')) $row['name'] = $m;
                if (Schema::hasColumn('add_months', 'month')) $row['month'] = $m;

                if (Schema::hasColumn('add_months', 'isActived')) $row['isActived'] = 1;
                if (Schema::hasColumn('add_months', 'isDeleted')) $row['isDeleted'] = 0;
                if (Schema::hasColumn('add_months', 'created_at')) $row['created_at'] = now();
                if (Schema::hasColumn('add_months', 'updated_at')) $row['updated_at'] = now();

                $rows[] = $row;
            }
            DB::table('add_months')->insert($rows);
        }

        // ---------- add_classes ----------
        if (DB::table('add_classes')->count() == 0) {
            $classes = ['Nursery','KG','One','Two','Three','Four','Five','Six','Seven','Eight','Nine','Ten'];
            $rows = [];
            foreach ($classes as $c) {
                $row = [];
                if (Schema::hasColumn('add_classes', 'name')) $row['name'] = $c;
                if (Schema::hasColumn('add_classes', 'class_name')) $row['class_name'] = $c;

                if (Schema::hasColumn('add_classes', 'isActived')) $row['isActived'] = 1;
                if (Schema::hasColumn('add_classes', 'isDeleted')) $row['isDeleted'] = 0;
                if (Schema::hasColumn('add_classes', 'created_at')) $row['created_at'] = now();
                if (Schema::hasColumn('add_classes', 'updated_at')) $row['updated_at'] = now();

                $rows[] = $row;
            }
            DB::table('add_classes')->insert($rows);
        }

        // ---------- add_sections ----------
        if (DB::table('add_sections')->count() == 0) {
            $sections = ['A','B','C'];
            $rows = [];
            foreach ($sections as $s) {
                $row = [];
                if (Schema::hasColumn('add_sections', 'name')) $row['name'] = $s;
                if (Schema::hasColumn('add_sections', 'section_name')) $row['section_name'] = $s;

                if (Schema::hasColumn('add_sections', 'isActived')) $row['isActived'] = 1;
                if (Schema::hasColumn('add_sections', 'isDeleted')) $row['isDeleted'] = 0;
                if (Schema::hasColumn('add_sections', 'created_at')) $row['created_at'] = now();
                if (Schema::hasColumn('add_sections', 'updated_at')) $row['updated_at'] = now();

                $rows[] = $row;
            }
            DB::table('add_sections')->insert($rows);
        }
    }
}
