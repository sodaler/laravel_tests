<?php

namespace App\Rules;

use Closure;
use http\Message;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

class ValidProtocol implements Rule
{
    public function passes($attribute, $value): bool
    {
        return Str::startsWith($value, ['http://', 'https://']);
    }

    public function message(): string
    {
        return 'The URL must be include the http protocol';
    }
}
