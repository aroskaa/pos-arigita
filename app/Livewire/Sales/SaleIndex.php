<?php

namespace App\Livewire\Sales;

use App\Models\Sale;
use Livewire\Component;
use Livewire\WithPagination;

class SaleIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public ?string $startDate = null;

    public ?string $endDate = null;

    public string $datePreset = 'all';

    public int $filterKey = 0;

    protected string $paginationTheme = 'tailwind';

    public function setDatePreset(string $preset): void
    {
        $this->datePreset = $preset;

        if ($preset === 'today') {
            $this->startDate = now()->format('Y-m-d');
            $this->endDate = now()->format('Y-m-d');
        } elseif ($preset === '7days') {
            $this->startDate = now()->subDays(6)->format('Y-m-d');
            $this->endDate = now()->format('Y-m-d');
        } elseif ($preset === 'this_month') {
            $this->startDate = now()->startOfMonth()->format('Y-m-d');
            $this->endDate = now()->format('Y-m-d');
        } elseif ($preset === 'last_month') {
            $this->startDate = now()->subMonth()->startOfMonth()->format('Y-m-d');
            $this->endDate = now()->subMonth()->endOfMonth()->format('Y-m-d');
        } else {
            $this->startDate = null;
            $this->endDate = null;
        }

        $this->filterKey++;
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.sales.sale-index', [
            'sales' => Sale::query()
                ->with(['cashier', 'customer', 'items'])
                ->when($this->search, function ($query) {
                    $query->where(function ($saleQuery) {
                        $saleQuery->where('invoice_number', 'like', '%' . $this->search . '%')
                            ->orWhereHas('customer', function ($customerQuery) {
                                $customerQuery->where('name', 'like', '%' . $this->search . '%')
                                    ->orWhere('phone', 'like', '%' . $this->search . '%');
                            });
                    });
                })
                ->when($this->startDate, function ($query) {
                    $query->whereDate('sale_date', '>=', $this->startDate);
                })
                ->when($this->endDate, function ($query) {
                    $query->whereDate('sale_date', '<=', $this->endDate);
                })
                ->latest()
                ->paginate(7),

            'todaySales' => Sale::query()
                ->where('status', '!=', 'cancelled')
                ->whereDate('sale_date', '=', now()->toDateString())
                ->count(),

            'todayRevenue' => Sale::query()
                ->where('status', '!=', 'cancelled')
                ->whereDate('sale_date', '=', now()->toDateString())
                ->sum('grand_total'),
        ]);
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->startDate = null;
        $this->endDate = null;
        $this->datePreset = 'all';

        $this->filterKey++;

        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStartDate(): void
    {
        $this->datePreset = 'custom';
        $this->resetPage();
    }

    public function updatedEndDate(): void
    {
        $this->datePreset = 'custom';
        $this->resetPage();
    }
}
