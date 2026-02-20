<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class TransactionsType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'key', 'isActived', 'isDeleted'];

    public const CORE_KEYS = [
        'student_fee',
        'donation',
        'income',
        'expense',
        'loan_taken',
        'loan_repayment',
    ];

    public static function idByKey(string $key): int
    {
        if (!in_array($key, self::CORE_KEYS, true)) {
            throw new \InvalidArgumentException("Invalid transactions type key: {$key}");
        }

        return Cache::rememberForever("tx_type_id:{$key}", function () use ($key) {
            $id = (int) static::query()->where('key', $key)->value('id');
            if (!$id) {
                throw new \RuntimeException("Missing transactions_types row for key [{$key}]. Run seed.");
            }
            return $id;
        });
    }

    protected static function booted()
    {
        $flush = function ($m) {
            if (!empty($m->key)) Cache::forget("tx_type_id:{$m->key}");
        };
        static::saved($flush);
        static::deleted($flush);
    }
}
