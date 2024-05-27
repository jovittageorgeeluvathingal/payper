<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\User;
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
        ]);

        // Retrieve sender and receiver
        $sender = User::findOrFail($request->sender_id);
        $receiver = User::findOrFail($request->receiver_id);

        // Verify sender's password
        if (!password_verify($request->password, $sender->password)) {
            return response()->json(['error' => 'Incorrect password'], 403);
        }

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
        // $transaction->  charge = $sender->transaction_charge;
        $transaction-> balance =$sender->balance - $totalAmount;
           // Assuming all transactions are immediately completed
        
        $transaction->save();

        // Update sender's balance
        $sender->balance -= $totalAmount;
        $sender->save();

        // Update receiver's balance
        $receiver->balance += $request->amount;
        $receiver->save();

        return response()->json(['message' => 'Transaction successful'], 200);
    }
}
        

    
