<?php

namespace App\Livewire\StockDailyRecaps;

use Livewire\Component;

class StockDailyRecapIndex extends Component
{
    public string $recapDate;

    public function mount(): void
    {
        $this->recapDate = now()->toDateString();
    }

    public function render()
    {
        return view('livewire.stock-daily-recaps.stock-daily-recap-index');
    }
}
