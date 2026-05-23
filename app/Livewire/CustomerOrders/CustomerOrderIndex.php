<?php

namespace App\Livewire\CustomerOrders;

use App\Models\CustomerOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\WithPagination;

use App\Models\Sale;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class CustomerOrderIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = '';

    public bool $showDetailModal = false;

    public bool $showRejectModal = false;

    public ?int $selectedOrderId = null;

    public array $detailOrder = [];

    public bool $showCancelModal = false;

    public ?int $cancelOrderId = null;

    public ?string $cancelReason = null;

    public ?array $cancelOrderPreview = null;

    public ?string $rejectionNote = null;

    protected string $paginationTheme = 'tailwind';

    public function render()
    {
        return view('livewire.customer-orders.customer-order-index', [
            'orders' => CustomerOrder::query()
                ->with(['items.product', 'customer', 'converter', 'rejecter', 'canceller', 'sale.items.product'])
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
            ->with(['items.product', 'converter', 'rejecter', 'canceller', 'sale'])
            ->findOrFail($orderId);

        $this->selectedOrderId = $order->id;

        $this->detailOrder = [
            'id' => $order->id,
            'invoice_number' => $order->sale?->invoice_number,
            'sale_status' => $order->sale?->status,
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

    public function openCancelModal(int $orderId): void
    {
        $order = CustomerOrder::query()
            ->with('sale')
            ->findOrFail($orderId);

        if ($order->status !== 'converted') {
            Session::flash('error', 'Hanya order converted yang dapat dibatalkan.');
            return;
        }

        if (! $order->sale) {
            Session::flash('error', 'Transaksi POS untuk order ini tidak ditemukan.');
            return;
        }

        if ($order->sale->status === 'cancelled') {
            Session::flash('error', 'Transaksi ini sudah pernah dibatalkan.');
            return;
        }

        $this->cancelOrderId = $order->id;
        $this->cancelReason = null;

        $this->cancelOrderPreview = [
            'order_number' => $order->order_number,
            'invoice_number' => $order->sale->invoice_number,
            'customer_name' => $order->customer_name,
            'grand_total' => (float) $order->sale->grand_total,
        ];

        $this->resetValidation();

        $this->showCancelModal = true;
    }

    public function cancelConvertedOrder(): void
    {
        $this->validate([
            'cancelReason' => ['required', 'string', 'min:5', 'max:1000'],
        ], [
            'cancelReason.required' => 'Alasan pembatalan wajib diisi.',
            'cancelReason.min' => 'Alasan pembatalan minimal 5 karakter.',
        ]);

        DB::beginTransaction();

        try {
            $order = CustomerOrder::query()
                ->with(['sale.items.product', 'sale.customer'])
                ->lockForUpdate()
                ->findOrFail($this->cancelOrderId);

            if ($order->status !== 'converted') {
                throw new \Exception('Hanya order converted yang dapat dibatalkan.');
            }

            $sale = $order->sale;

            if (! $sale) {
                throw new \Exception('Transaksi POS untuk order ini tidak ditemukan.');
            }

            if ($sale->status === 'cancelled') {
                throw new \Exception('Transaksi ini sudah pernah dibatalkan.');
            }

            $customerName = $order->customer_name ?: ($sale->customer?->name ?? 'Customer');
            $movementNote = "{$sale->invoice_number} - {$customerName} - {$this->cancelReason}";

            foreach ($sale->items as $item) {
                $product = $item->product;

                if (! $product) {
                    continue;
                }

                $product->refresh();

                $stockBefore = (int) $product->stock;
                $quantityReturn = (int) $item->quantity;
                $stockAfter = $stockBefore + $quantityReturn;

                $averageCost = (float) $product->average_cost;

                $product->update([
                    'stock' => $stockAfter,
                ]);

                StockMovement::query()->create([
                    'product_id' => $product->id,
                    'type' => 'order_cancel',
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                    'quantity_in' => $quantityReturn,
                    'quantity_out' => 0,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'average_cost_before' => $averageCost,
                    'average_cost_after' => $averageCost,
                    'note' => $movementNote,
                    'created_by' => Auth::id(),
                ]);
            }

            $sale->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancelled_by' => Auth::id(),
                'cancel_note' => $this->cancelReason,
            ]);

            $order->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancelled_by' => Auth::id(),
                'cancel_note' => $this->cancelReason,
            ]);

            DB::commit();

            Session::flash('success', 'Order converted berhasil dibatalkan dan stok telah dikembalikan.');

            $this->showCancelModal = false;
            $this->cancelOrderId = null;
            $this->cancelReason = null;
            $this->cancelOrderPreview = null;

            $this->dispatch('$refresh');
        } catch (\Throwable $e) {
            DB::rollBack();

            Session::flash('error', $e->getMessage());

            $this->showCancelModal = false;
        }
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
