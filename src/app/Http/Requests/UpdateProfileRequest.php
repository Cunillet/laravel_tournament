<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'nickname'        => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                'unique:users,nickname,' . $userId,
            ],
            'email'           => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email,' . $userId,
            ],
            'current_password' => ['required', 'string', 'current_password'],
        ];
    }
}
