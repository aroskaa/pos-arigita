<?php

namespace App\Livewire\CustomerOrders;

use App\Models\CustomerOrder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerOrderHistory extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = 'all';

    public ?int $selectedOrderId = null;

    protected string $paginationTheme = 'tailwind';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function setStatusFilter(string $status): void
    {
        if (in_array($status, ['all', 'pending', 'converted', 'rejected', 'cancelled'], true)) {
            $this->statusFilter = $status;
            $this->resetPage();
        }
    }

    public function toggleDetail(int $orderId): void
    {
        if ($this->selectedOrderId === $orderId) {
            $this->selectedOrderId = null;
        } else {
            $this->selectedOrderId = $orderId;
        }
    }

    public function render()
    {
        $user = Auth::user();
        $customer = $user?->customer;

        $orders = CustomerOrder::query()
            ->with(['items.product.unit', 'sale'])
            ->where(function ($query) use ($user, $customer) {
                if ($customer) {
                    $query->where('customer_id', $customer->id);
                }
                if ($user?->phone) {
                    $query->orWhere('customer_phone', $user->phone);
                }
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->search !== '', function ($query) {
                $query->where(function ($sub) {
                    $sub->where('order_number', 'like', '%' . $this->search . '%')
                        ->orWhereHas('items.product', function ($pQuery) {
                            $pQuery->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->latest()
            ->paginate(10);

        $counts = [
            'all' => CustomerOrder::query()
                ->where(function ($query) use ($user, $customer) {
                    if ($customer) {
                        $query->where('customer_id', $customer->id);
                    }
                    if ($user?->phone) {
                        $query->orWhere('customer_phone', $user->phone);
                    }
                })->count(),
            'pending' => CustomerOrder::query()
                ->where(function ($query) use ($user, $customer) {
                    if ($customer) {
                        $query->where('customer_id', $customer->id);
                    }
                    if ($user?->phone) {
                        $query->orWhere('customer_phone', $user->phone);
                    }
                })->where('status', 'pending')->count(),
            'converted' => CustomerOrder::query()
                ->where(function ($query) use ($user, $customer) {
                    if ($customer) {
                        $query->where('customer_id', $customer->id);
                    }
                    if ($user?->phone) {
                        $query->orWhere('customer_phone', $user->phone);
                    }
                })->where('status', 'converted')->count(),
            'rejected' => CustomerOrder::query()
                ->where(function ($query) use ($user, $customer) {
                    if ($customer) {
                        $query->where('customer_id', $customer->id);
                    }
                    if ($user?->phone) {
                        $query->orWhere('customer_phone', $user->phone);
                    }
                })->where('status', 'rejected')->count(),
            'cancelled' => CustomerOrder::query()
                ->where(function ($query) use ($user, $customer) {
                    if ($customer) {
                        $query->where('customer_id', $customer->id);
                    }
                    if ($user?->phone) {
                        $query->orWhere('customer_phone', $user->phone);
                    }
                })->where('status', 'cancelled')->count(),
        ];

        return view('livewire.customer-orders.customer-order-history', [
            'orders' => $orders,
            'counts' => $counts,
            'user' => $user,
            'customer' => $customer,
        ]);
    }
}
