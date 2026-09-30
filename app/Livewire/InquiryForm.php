<?php

namespace App\Livewire;

use App\Models\Inquiry;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Validate;
use Livewire\Component;

class InquiryForm extends Component
{
    #[Validate('required|string|max:100')]
    public string $name = '';

    #[Validate('required|string|max:30')]
    public string $phone = '';

    #[Validate('nullable|email|max:150')]
    public string $email = '';

    #[Validate('nullable|in:fragrance,gift,by_calone,skincare,delivery,other')]
    public string $event_type = 'fragrance';

    #[Validate('nullable|date|after_or_equal:today')]
    public string $event_date = '';

    #[Validate('nullable|integer|min:1|max:5000')]
    public ?int $guests = null;

    #[Validate('nullable|string|max:2000')]
    public string $message = '';

    public bool $sent = false;

    public function mount(): void
    {
        if ($user = auth()->user()) {
            $this->name = $user->name;
            $this->email = $user->email;
        }
    }

    public function submit(): void
    {
        $data = $this->validate();

        $key = 'inquiry:'.request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('message', __('Too many attempts. Please try again later.'));

            return;
        }
        RateLimiter::hit($key, 3600);

        Inquiry::create([
            ...$data,
            'event_date' => $data['event_date'] ?: null,
            'user_id' => auth()->id(),
        ]);

        $this->reset('phone', 'event_date', 'guests', 'message');
        $this->sent = true;
    }

    public function render()
    {
        return view('livewire.inquiry-form');
    }
}
