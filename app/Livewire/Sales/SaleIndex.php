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

    protected string $paginationTheme = 'tailwind';

    public function render()
    {
        return view('livewire.sales.sale-index', [
            'sales' => Sale::query()
                ->with(['cashier', 'items'])
                ->when($this->search, function ($query) {
                    $query->where('invoice_number', 'like', '%' . $this->search . '%');
                })
                ->when($this->startDate, function ($query) {
                    $query->whereDate('sale_date', '>=', $this->startDate);
                })
                ->when($this->endDate, function ($query) {
                    $query->whereDate('sale_date', '<=', $this->endDate);
                })
                ->latest()
                ->paginate(10),

            'todaySales' => Sale::query()
                ->whereDate('sale_date', '=', now()->toDateString())
                ->count(),

            'todayRevenue' => Sale::query()
                ->whereDate('sale_date', '=', now()->toDateString())
                ->sum('grand_total'),
        ]);
    }
}
