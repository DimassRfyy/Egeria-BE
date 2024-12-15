<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingTransactionRequest;
use App\Http\Resources\Api\BookingTransactionApiResource;
use App\Models\BookingTransaction;
use App\Models\Cosmetic;
use Illuminate\Http\Request;

class BookingTransactionController extends Controller
{
    public function store(StoreBookingTransactionRequest $request) {
        try {
           $validateData = $request->validated();

           if($request->hasFile('proof')) {
            $filePath = $request->file('proof')->store('proofs', 'public');
            $validateData['proof'] = $filePath;
           }

           $products = $request->input('cosmetic_ids');
           $totalQuantity = 0;
           $totalPrice = 0;

           $cosmeticIds = array_column($products, 'id');
           $cosmetics = Cosmetic::whereIn('id', $cosmeticIds)->get();

           foreach ($products as $product) {
               $cosmetic = $cosmetics->firstWhere('id', $product['id']);
               $totalQuantity += $product['quantity'];
               $totalPrice += $cosmetic->price * $product['quantity'];
           }

           $tax = $totalPrice * 0.11;
           $grandTotal = $totalPrice + $tax;

           $validateData['total_amount'] = $grandTotal;
           $validateData['total_tax_amount'] = $tax;
           $validateData['sub_total_amount'] = $totalPrice;
           $validateData['is_paid'] = false;
           $validateData['trx_id'] = BookingTransaction::generateUniqueTrxId();
           $validateData['quantity'] = $totalQuantity;

           $bookingTransaction = BookingTransaction::create($validateData);

            foreach ($products as $product) {
                $cosmetic = $cosmetics->firstWhere('id', $product['id']);
                $bookingTransaction->transactionDetails()->create([
                     'cosmetic_id' => $cosmetic->id,
                     'quantity' => $product['quantity'],
                     'price' => $cosmetic->price,
                ]);
            }
            
            return new BookingTransactionApiResource($bookingTransaction->load('transactionDetails'));

        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function booking_details(Request $request) {
        $validated = $request->validate([
            'trx_id' => 'required|string|exists:booking_transactions,trx_id',
            'email' => 'required|email',
        ]);

        $booking = BookingTransaction::where('trx_id', $request->trx_id)->where('email', $request->email)
            ->with([
                'transactionDetails.cosmetic',
                'transactionDetails',
            ])->first();

        if(!$booking) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        return new BookingTransactionApiResource($booking);
    }
}
