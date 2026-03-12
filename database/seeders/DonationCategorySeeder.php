<?php

namespace Database\Seeders;

use App\Support\Donations\DonationCategorySync;
use Illuminate\Database\Seeder;

class DonationCategorySeeder extends Seeder
{
    public function run(): void
    {
        $sync = app(DonationCategorySync::class);

        $sync->syncCategories();
        $sync->backfillDonationIntents();
        $sync->backfillDonationRecords();
    }
}
