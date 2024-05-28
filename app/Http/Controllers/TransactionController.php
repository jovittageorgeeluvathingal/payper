<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\User;
use App\Models\TransactionPurpose;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class TransactionController extends Controller
{   public function makeTransaction(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01', // Assuming minimum amount is 0.01
            'password' => 'required', // Assuming password validation
            'notes' => 'nullable|string',
        ]);

        // Retrieve sender and receiver
        $sender = User::findOrFail($request->sender_id);
        $receiver = User::findOrFail($request->receiver_id);

        // Verify sender's password
        if (!password_verify($request->password, $sender->password)) {
            return response()->json(['error' => 'Incorrect password'], 403);
        }
 // Calculate charge (10% of the transaction amount)
 $charge = $request->amount * 0.10; // 10% charge

 // Calculate total amount (including charge)
 $totalAmount = $request->amount + $charge;


        // Calculate total amount with charge
        $totalAmount = $request->amount + $sender->transaction_charge;

        // Check if sender has sufficient balance
        if ($sender->balance < $totalAmount) {
            return response()->json(['error' => 'Insufficient balance'], 400);
        }

        // Create a new transaction
        $transaction = new Transaction;
        $transaction-> sender_id= $sender->id;
        $transaction ->  receiver_id = $receiver->id;
        $transaction -> amount = $request->amount;
       $transaction->  charge = ($request->transaction_charge)?($request->transaction_charge):0;
        $transaction-> balance =$sender->balance - $totalAmount;
        $transaction-> notes=$sender->notes;
           // Assuming all transactions are immediately completed
        
        $transaction->save();

        // Update sender's balance
        $sender->balance -= $totalAmount;
        $sender->save();

        // Update receiver's balance
        $receiver->balance += $request->amount;
        $receiver->save();

        // return response()->json(['message' => 'Transaction successful'], 200);
        return response()->json([
            'message' => 'Transaction successful',
            'transaction_id' => $transaction->id,
            'amount' => $transaction->amount,
            'charge' => $transaction->charge,
            'total' => $totalAmount,
            'notes'=>$transaction->notes,
            'timestamp' => $transaction->created_at->format('m/d/y, h:i A'),
         ], 200);
    }
   
    public function getpurpose($purposeid){
        $purpose = TransactionPurpose::find($purposeid);

        return response()->json([
            'id' => $purpose->id,
            'name' => $purpose->name,
            'charge' => $purpose->charge, ], 200);
    }
} 
        

    
