<?php

namespace App\Models;

use App\Enums\Currency;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'ages', 'currency_id', 'start_date', 'end_date', 'trip_length', 'total'])]
class Quotation extends Model
{
    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'ages' => 'array',
            'currency_id' => Currency::class,
            'start_date' => 'immutable_date',
            'end_date' => 'immutable_date',
            'total' => 'decimal:2',
        ];
    }
}
