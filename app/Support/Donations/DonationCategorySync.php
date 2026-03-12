<?php

namespace App\Support\Donations;

use App\Models\DonationCategory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DonationCategorySync
{
    public function syncCategories(): void
    {
        if (! Schema::hasTable('donation_categories')) {
            return;
        }

        DonationCategory::query()->upsert(
            $this->catalogRows(),
            ['key'],
            ['slug', 'name', 'label', 'description', 'badge', 'sort_order', 'is_active', 'is_featured', 'updated_at']
        );
    }

    public function backfillDonationIntents(): void
    {
        if (! Schema::hasTable('donation_intents') || ! Schema::hasColumn('donation_intents', 'donation_category_id')) {
            return;
        }

        $categoryIdsByKey = $this->categoryIdsByKey();

        if ($categoryIdsByKey === []) {
            return;
        }

        DB::table('donation_intents')
            ->select(['id', 'metadata'])
            ->whereNull('donation_category_id')
            ->orderBy('id')
            ->chunkById(100, function ($intents) use ($categoryIdsByKey): void {
                foreach ($intents as $intent) {
                    $categoryKey = $this->extractCategoryKey($this->decodeMetadata($intent->metadata));
                    $categoryId = $categoryKey ? ($categoryIdsByKey[$categoryKey] ?? null) : null;

                    if (! $categoryId) {
                        continue;
                    }

                    DB::table('donation_intents')
                        ->where('id', $intent->id)
                        ->update([
                            'donation_category_id' => $categoryId,
                        ]);
                }
            });
    }

    public function backfillDonationRecords(): void
    {
        if (! Schema::hasTable('donation_records') || ! Schema::hasColumn('donation_records', 'donation_category_id')) {
            return;
        }

        $categoryIdsByKey = $this->categoryIdsByKey();

        DB::table('donation_records')
            ->select(['id', 'donation_intent_id', 'donation_category_id', 'metadata'])
            ->orderBy('id')
            ->chunkById(100, function ($records) use ($categoryIdsByKey): void {
                $intentIds = collect($records)
                    ->pluck('donation_intent_id')
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                $intents = DB::table('donation_intents')
                    ->whereIn('id', $intentIds)
                    ->get(['id', 'donation_category_id', 'metadata'])
                    ->keyBy('id');

                foreach ($records as $record) {
                    $recordMetadata = $this->decodeMetadata($record->metadata);
                    $intent = $intents->get($record->donation_intent_id);
                    $intentMetadata = $this->decodeMetadata($intent?->metadata);
                    $categorySnapshot = $this->extractCategorySnapshot($recordMetadata);

                    if ($categorySnapshot === []) {
                        $categorySnapshot = $this->extractCategorySnapshot($intentMetadata);
                    }

                    $updates = [];

                    if ($record->donation_category_id === null) {
                        $categoryId = $intent?->donation_category_id;

                        if (! $categoryId) {
                            $categoryKey = $categorySnapshot['key'] ?? null;
                            $categoryId = $categoryKey ? ($categoryIdsByKey[$categoryKey] ?? null) : null;
                        }

                        if ($categoryId) {
                            $updates['donation_category_id'] = $categoryId;
                        }
                    }

                    if ($categorySnapshot !== [] && $this->extractCategorySnapshot($recordMetadata) !== $categorySnapshot) {
                        $recordMetadata['category'] = $categorySnapshot;
                        $updates['metadata'] = $this->encodeMetadata($recordMetadata);
                    }

                    if ($updates === []) {
                        continue;
                    }

                    DB::table('donation_records')
                        ->where('id', $record->id)
                        ->update($updates);
                }
            });
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function catalogRows(): array
    {
        $catalog = GuestDonationCategoryCatalog::all();
        $now = now();

        return array_values(array_map(
            function (array $category, int $index) use ($now): array {
                $key = (string) $category['key'];

                return [
                    'key' => $key,
                    'slug' => Str::of($key)->replace('_', '-')->toString(),
                    'name' => Str::of($key)->replace('_', ' ')->title()->toString(),
                    'label' => (string) ($category['label'] ?? Str::of($key)->replace('_', ' ')->title()->toString()),
                    'description' => $category['description'] ?? null,
                    'badge' => $category['badge'] ?? null,
                    'sort_order' => (int) ($category['sort_order'] ?? (($index + 1) * 10)),
                    'is_active' => array_key_exists('is_active', $category) ? (bool) $category['is_active'] : true,
                    'is_featured' => (bool) ($category['featured'] ?? $category['is_featured'] ?? false),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            },
            $catalog,
            array_keys($catalog),
        ));
    }

    /**
     * @return array<string, int>
     */
    private function categoryIdsByKey(): array
    {
        if (! Schema::hasTable('donation_categories')) {
            return [];
        }

        return DonationCategory::query()->pluck('id', 'key')->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeMetadata(mixed $metadata): array
    {
        if (is_array($metadata)) {
            return $metadata;
        }

        if (! is_string($metadata) || trim($metadata) === '') {
            return [];
        }

        $decoded = json_decode($metadata, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function encodeMetadata(array $metadata): string
    {
        return (string) json_encode($metadata, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function extractCategoryKey(array $metadata): ?string
    {
        $key = Arr::get($metadata, 'category.key');

        return is_string($key) && $key !== '' ? $key : null;
    }

    /**
     * @return array<string, string>
     */
    private function extractCategorySnapshot(array $metadata): array
    {
        return array_filter([
            'key' => $this->extractCategoryKey($metadata),
            'label' => $this->extractCategoryLabel($metadata),
        ], static fn ($value): bool => is_string($value) && $value !== '');
    }

    private function extractCategoryLabel(array $metadata): ?string
    {
        $label = Arr::get($metadata, 'category.label');

        return is_string($label) && $label !== '' ? $label : null;
    }
}
