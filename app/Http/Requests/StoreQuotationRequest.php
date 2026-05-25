<?php

namespace App\Http\Requests;

use App\Enums\Currency;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuotationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'age' => [
                'required',
                'string',
                Closure::fromCallable([$this, 'validateTravellerAges']),
            ],
            'currency_id' => ['required', Rule::enum(Currency::class)],
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ];
    }

    /**
     * @return list<int>
     */
    public function ages(): array
    {
        return array_map('intval', explode(',', $this->validated('age')));
    }

    private function validateTravellerAges(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || preg_match('/^\d+(,\d+)*$/', $value) !== 1) {
            $fail('The age field must be a comma-separated list of whole-number ages.');

            return;
        }

        foreach (explode(',', $value) as $age) {
            if ((int) $age < 18 || (int) $age > 70) {
                $fail('Each traveller age must be between 18 and 70.');

                return;
            }
        }
    }
}
