<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'level' => ['required', 'in:beginner,intermediate,advanced'],
        ];

        // Règles spécifiques aux tuteurs
        if ($user->isTutor()) {
            $rules['skills'] = ['nullable', 'array'];
            $rules['skills.*'] = ['string', 'max:100'];
            $rules['hourly_rate'] = ['nullable', 'numeric', 'min:5', 'max:200'];
            $rules['is_available'] = ['boolean'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'phone.max' => 'Le numéro de téléphone ne peut pas dépasser 20 caractères.',
            'bio.max' => 'La bio ne peut pas dépasser 1000 caractères.',
            'level.required' => 'Le niveau est obligatoire.',
            'level.in' => 'Le niveau doit être débutant, intermédiaire ou avancé.',
            'skills.array' => 'Les compétences doivent être une liste.',
            'skills.*.string' => 'Chaque compétence doit être un texte.',
            'skills.*.max' => 'Chaque compétence ne peut pas dépasser 100 caractères.',
            'hourly_rate.numeric' => 'Le tarif horaire doit être un nombre.',
            'hourly_rate.min' => 'Le tarif horaire minimum est de 5€.',
            'hourly_rate.max' => 'Le tarif horaire maximum est de 200€.',
        ];
    }
}
