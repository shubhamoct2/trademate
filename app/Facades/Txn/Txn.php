<?php

namespace App\Facades\Txn;

use App\Enums\TxnStatus;
use App\Enums\TxnType;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Str;

class Txn
{
    /**
     * @param  null  $payCurrency
     * @param  null  $payAmount
     * @param  null  $userID
     * @param  null  $fromUserID
     * @param  string  $fromModel
     * @param  array  $manualDepositData
     */
    public function new(
        $amount, 
        $charge, 
        $final_amount, 
        $method, 
        $description, 
        string|TxnType $type, 
        string|TxnStatus $status = TxnStatus::Pending, 
        $payCurrency = null, 
        $payAmount = null, 
        $userID = null, 
        $relatedUserID = null, 
        $relatedModel = 'User', 
        array $manualFieldData = [], 
        string $approvalCause = 'none', 
        $targetId = null, 
        $targetType = null, 
        $isLevel = false, 
        $address = null): Transaction
    {
        if ($type == 'withdraw') {
            self::withdrawBalance($amount);
        }

        $transaction = new Transaction();
        $transaction->user_id = $userID ?? Auth::user()->id;
        $transaction->from_user_id = $relatedUserID;
        $transaction->from_model = $relatedModel;
        $transaction->tnx = 'TRX'.strtoupper(Str::random(10));
        $transaction->description = $description;
        $transaction->amount = $amount;
        $transaction->type = $type;
        $transaction->charge = $charge;
        $transaction->final_amount = $final_amount;
        $transaction->method = $method;
        $transaction->pay_currency = $payCurrency;
        $transaction->pay_amount = $payAmount;
        $transaction->address = $address;
        $transaction->manual_field_data = json_encode($manualFieldData);
        $transaction->approval_cause = $approvalCause;
        $transaction->target_id = $targetId;
        $transaction->target_type = $targetType;
        $transaction->is_level = $isLevel;
        $transaction->status = $status;
        $transaction->save();

        return $transaction;
    }

    /**
     * @param $walletName
     */
    private function withdrawBalance($amount): void
    {
        User::find(auth()->user()->id)->removeMoney($amount);
    }

    public function update(
        $tnx, 
        $status, 
        $userId,
        $approvalCause = 'none', 
        $final_amount = null, 
        $pay_amount = null, 
        $txID = null,
        $type = 0 // 0: deposit, 1: withdrawal
    )
    {
        $transaction = Transaction::tnx($tnx);

        $user = $transaction->user;

        if ($final_amount && $pay_amount && $txID) {
            if ($status == TxnStatus::Success && $transaction->type == TxnType::Deposit) {
                if ($type == 0)
                    $user->increment('balance', floatval($pay_amount));
                else
                    $user->decrement('balance', floatval($pay_amount));
            }

            $data = [
                'status' => $status,
                'approval_cause' => $approvalCause,
                'final_amount' => $final_amount,
                'pay_amount' => $pay_amount,
                'txID' => $txID,
            ];
        } else {
            if ($status == TxnStatus::Success && ($transaction->type == TxnType::Deposit || $transaction->type == TxnType::ManualDeposit)) {
                $amount = $transaction->amount;
                $user->increment('balance', $amount);
            }

            $data = [
                'status' => $status,
                'approval_cause' => $approvalCause,
            ];
        }

        return $transaction->update($data);

    }
}
