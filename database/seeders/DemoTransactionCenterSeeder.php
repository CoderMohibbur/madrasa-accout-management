<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoTransactionCenterSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        /**
         * ---------------------------------------
         * 0) Ensure at least 1 user exists (for lenders.users_id FK)
         * ---------------------------------------
         */
        $usersColumns = DB::getSchemaBuilder()->getColumnListing('users');

        $userId = DB::table('users')->value('id');

        if (!$userId) {
            // minimal user payload (only keep existing columns)
            $user = [
                'name'              => 'Seeder Admin',
                'email'             => 'admin@example.com',
                'password'          => Hash::make('password'),
                'email_verified_at' => $now,
                'remember_token'    => Str::random(10),
                'created_at'        => $now,
                'updated_at'        => $now,

                // যদি আপনার users table-এ এগুলো থাকে
                'isActived'         => 1,
                'isDeleted'         => 0,
            ];

            $user = array_intersect_key($user, array_flip($usersColumns));
            $userId = DB::table('users')->insertGetId($user);
        }

        /**
         * ---------------------------------------
         * 1) Accounts (required columns)
         * accounts: name, account_number, account_details,
         *          opening_balance, current_balance, isActived, isDeleted
         * ---------------------------------------
         */
        $accountsColumns = DB::getSchemaBuilder()->getColumnListing('accounts');

        if (DB::table('accounts')->count() === 0) {
            $acc1 = [
                'name'            => 'Cash',
                'account_number'  => 'AC-1001',
                'account_details' => 'Cash in hand',
                'opening_balance' => 0,
                'current_balance' => 0,
                'isActived'       => 1,
                'isDeleted'       => 0,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];

            $acc2 = [
                'name'            => 'Bank',
                'account_number'  => 'AC-1002',
                'account_details' => 'Bank account',
                'opening_balance' => 0,
                'current_balance' => 0,
                'isActived'       => 1,
                'isDeleted'       => 0,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];

            DB::table('accounts')->insert(array_intersect_key($acc1, array_flip($accountsColumns)));
            DB::table('accounts')->insert(array_intersect_key($acc2, array_flip($accountsColumns)));
        }

        /**
         * ---------------------------------------
         * 2) Students
         * students: first_name,last_name,full_name,dob,roll,email,mobile,photo,age,
         *          fees_type_id,class_id,section_id,academic_year_id,scholarship_amount,
         *          isActived,isDeleted
         * NOTE: fees_type_id/class_id/section_id/academic_year_id সব nullable, তাই null রাখছি।
         * isActived required, তাই অবশ্যই সেট করছি।
         * ---------------------------------------
         */
        $studentsColumns = DB::getSchemaBuilder()->getColumnListing('students');

        if (DB::table('students')->count() === 0) {
            $s1 = [
                'first_name' => 'Test',
                'last_name'  => 'Student 1',
                'full_name'  => 'Test Student 1',
                'roll'       => 1,
                'email'      => 'student1@example.com',
                'mobile'     => '01700000001',
                'dob'        => '2010-01-01',
                'isActived'  => 1,
                'isDeleted'  => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $s2 = [
                'first_name' => 'Test',
                'last_name'  => 'Student 2',
                'full_name'  => 'Test Student 2',
                'roll'       => 2,
                'email'      => 'student2@example.com',
                'mobile'     => '01700000002',
                'dob'        => '2011-01-01',
                'isActived'  => 1,
                'isDeleted'  => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            DB::table('students')->insert(array_intersect_key($s1, array_flip($studentsColumns)));
            DB::table('students')->insert(array_intersect_key($s2, array_flip($studentsColumns)));
        }

        /**
         * ---------------------------------------
         * 3) Donors
         * donors: name,mobile,email,fees_type_id(nullable), isActived(required), isDeleted
         * ---------------------------------------
         */
        $donorsColumns = DB::getSchemaBuilder()->getColumnListing('donors');

        if (DB::table('donors')->count() === 0) {
            $d1 = [
                'name'       => 'Test Donor 1',
                'mobile'     => '01800000001',
                'email'      => 'donor1@example.com',
                'isActived'  => 1,
                'isDeleted'  => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            DB::table('donors')->insert(array_intersect_key($d1, array_flip($donorsColumns)));
        }

        /**
         * ---------------------------------------
         * 4) Lenders
         * lenders: name,phone,email,address,bank_detils,users_id(required FK), isActived(required)
         * ---------------------------------------
         */
        $lendersColumns = DB::getSchemaBuilder()->getColumnListing('lenders');

        if (DB::table('lenders')->count() === 0) {
            $l1 = [
                'name'        => 'Test Lender 1',
                'phone'       => '01900000001',
                'email'       => 'lender1@example.com',
                'address'     => 'Dhaka',
                'bank_detils' => 'Demo bank details',
                'users_id'    => $userId,
                'isActived'   => 1,
                'isDeleted'   => 0,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];

            DB::table('lenders')->insert(array_intersect_key($l1, array_flip($lendersColumns)));
        }
    }
}
