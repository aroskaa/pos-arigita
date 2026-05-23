<?php

namespace App\Livewire\CustomerOrders;

use App\Models\CustomerOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerOrderIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = '';

    public bool $showDetailModal = false;

    public bool $showRejectModal = false;

    public ?int $selectedOrderId = null;

    public array $detailOrder = [];

    public ?string $rejectionNote = null;

    protected string $paginationTheme = 'tailwind';

    public function render()
    {
        return view('livewire.customer-orders.customer-order-index', [
            'orders' => CustomerOrder::query()
                ->with(['items.product', 'customer', 'converter', 'rejecter', 'canceller'])
                ->when($this->search, function ($query) {
                    $query->where(function ($orderQuery) {
                        $orderQuery->where('order_number', 'like', '%' . $this->search . '%')
                            ->orWhere('customer_name', 'like', '%' . $this->search . '%')
                            ->orWhere('customer_phone', 'like', '%' . $this->search . '%');
                    });
                })
                ->when($this->status, function ($query) {
                    $query->where('status', $this->status);
                })
                ->latest()
                ->paginate(10),

            'statuses' => [
                'pending' => 'Pending',
                'converted' => 'Converted',
                'rejected' => 'Rejected',
                'cancelled' => 'Cancelled',
            ],
        ]);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'status',
        ]);

        $this->resetPage();
    }

    public function openDetail(int $orderId): void
    {
        $order = CustomerOrder::query()
            ->with(['items.product', 'converter', 'rejecter', 'canceller'])
            ->findOrFail($orderId);

        $this->selectedOrderId = $order->id;

        $this->detailOrder = [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'customer_name' => $order->customer_name,
            'customer_type' => $order->customer_type,
            'customer_phone' => $order->customer_phone,
            'customer_address' => $order->customer_address,
            'whatsapp_url' => $this->whatsappUrl($order->customer_phone),
            'status' => $order->status,
            'estimated_total' => (float) $order->estimated_total,
            'note' => $order->note,
            'created_at' => $order->created_at->format('d M Y H:i'),
            'converted_at' => $order->converted_at?->format('d M Y H:i'),
            'converted_by' => $order->converter?->name,
            'rejected_at' => $order->rejected_at?->format('d M Y H:i'),
            'rejected_by' => $order->rejecter?->name,
            'rejection_note' => $order->rejection_note,
            'cancelled_at' => $order->cancelled_at?->format('d M Y H:i'),
            'cancelled_by' => $order->canceller?->name,
            'cancel_note' => $order->cancel_note,
            'items' => $order->items->map(function ($item) {
                return [
                    'product_name' => $item->product->name,
                    'sku' => $item->product->sku,
                    'quantity' => $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'subtotal' => (float) $item->subtotal,
                ];
            })->toArray(),
        ];

        $this->showDetailModal = true;
    }

    public function openRejectModal(int $orderId): void
    {
        $order = CustomerOrder::query()->findOrFail($orderId);

        if ($order->status !== 'pending') {
            Session::flash('error', 'Hanya order pending yang dapat ditolak.');
            return;
        }

        $this->selectedOrderId = $order->id;
        $this->rejectionNote = null;
        $this->resetValidation();

        $this->showRejectModal = true;
    }

    public function rejectOrder(): void
    {
        $this->validate([
            'rejectionNote' => ['required', 'string', 'min:5', 'max:1000'],
        ], [
            'rejectionNote.required' => 'Alasan penolakan wajib diisi.',
            'rejectionNote.min' => 'Alasan penolakan minimal 5 karakter.',
        ]);

        $order = CustomerOrder::query()->findOrFail($this->selectedOrderId);

        if ($order->status !== 'pending') {
            Session::flash('error', 'Hanya order pending yang dapat ditolak.');
            $this->showRejectModal = false;
            return;
        }

        $order->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejected_by' => Auth::id(),
            'rejection_note' => $this->rejectionNote,
        ]);

        Session::flash('success', 'Order pelanggan berhasil ditolak.');

        $this->showRejectModal = false;
        $this->selectedOrderId = null;
        $this->rejectionNote = null;
    }

    public function processToPos(int $orderId): void
    {
        $order = CustomerOrder::query()->findOrFail($orderId);

        if ($order->status !== 'pending') {
            Session::flash('error', 'Hanya order pending yang dapat diproses ke POS.');
            return;
        }

        redirect()->route('pos.index', [
            'customer_order' => $order->id,
        ]);
    }

    public function whatsappUrl(?string $phone): ?string
    {
        if (! $phone) {
            return null;
        }

        $phone = preg_replace('/\D/', '', $phone);

        if ($phone === '') {
            return null;
        }

        if (str_starts_with($phone, '0')) {
            $phone = substr($phone, 1);
        }

        if (str_starts_with($phone, '62')) {
            return 'https://wa.me/' . $phone;
        }

        return 'https://wa.me/62' . $phone;
    }
}
