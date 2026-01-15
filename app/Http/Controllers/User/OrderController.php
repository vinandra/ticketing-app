<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DetailOrder;
use App\Models\Order;
use App\Models\Tiket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index() {
        $user = Auth::user() ?? \App\Models\User::find(1);
        $orders = Order::where('user_id', $user->id)->with('event')->orderBy('created_at', 'desc')->get();
        return view('orders.index', compact('orders'));
    }

    public function show(Order $order) 
    {
        $order->load('detailOrders.tiket', 'event');
        return view('orders.show', compact('order'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'event_id' => 'required|exists:events,id',
            'items' => 'required|array|min:1',
            'items.*.tiket_id' => 'required|integer|exists:tikets,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user();

        try {
            $order = DB::transaction(function () use ($data, $user) {
                $total = 0;

                foreach ($data['items'] as $it) {
                    $t = Tiket::lockForUpdate()->findOrFail($it['tiket_id']);
                    if ($t->stok < $it['quantity']) {
                        throw new \Exception("stok tiket tidak cukup untuk tiket: {$t->tipe}");
                    }
                    $total += ($t->harga ?? 0) * $it['quantity'];
                }

                $order = Order::create([
                    'user_id' => $user->id,
                    'event_id' => $data['event_id'],
                    'order_date' => Carbon::now(),
                    'total_harga' => $total,
                ]);

                foreach ($data['items'] as $it) {
                    $t = Tiket::findOrFail($it['tiket_id']);
                    $subtotal = ($t->harga ?? 0) * $it['quantity'];
                    DetailOrder::create([
                        'order_id' => $order->id,
                        'tiket_id' => $t->id,
                        'jumlah' => $it['quantity'],
                        'subtotal_harga' => $subtotal,
                    ]);

                    $t->stok = max(0, $t->stok - $it['quantity']);
                    $t->save();
                }

                return $order;
            });
            session()->flash('success', 'Pesanan berhasil dibuat.');

            return response()->json(['ok' => true, 'order_id' => $order->id, 'redirect' => route('orders.index')]);

        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
