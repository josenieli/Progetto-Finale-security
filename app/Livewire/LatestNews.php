<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\HttpService;
use Illuminate\Support\Facades\Log;

class LatestNews extends Component
{
    public string $selectedApi = '';

    protected array $allowedCountries = ['it', 'gb', 'us'];

    protected function rules(): array
    {
        return [
            'selectedApi' => ['required', 'string', 'in:it,gb,us'],
        ];
    }

    public function fetchNews(): void
    {
        $this->validate();

        if (!in_array($this->selectedApi, $this->allowedCountries, true)) {
            $this->news = ['error' => 'Invalid country'];
            return;
        }

        $url = 'https://newsapi.org/v2/top-headlines'
            . '?country=' . urlencode($this->selectedApi)
            . '&apiKey=' . config('services.newsapi.key');

        try {
            $this->news = json_decode(
                app(HttpService::class)->getRequest($url),
                true
            );
        } catch (\Throwable $e) {
            Log::error('Latest news fetch failed', [
                'country' => $this->selectedApi,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            $this->news = ['error' => 'Unable to load news'];
        }
    }

    public function render()
    {
        return view('livewire.latest-news');
    }
}