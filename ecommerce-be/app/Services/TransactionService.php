<?php   

namespace App\Services;
use App\Repositories\TransactionRepository;

class TransactionService
{
   
   private TransactionRepository $repository;
   
   public function __construct(TransactionRepository $repository){
        $this->repository = $repository;
   }

   public function markedAsReceived(int $transactionId)
    {
        $transaction = $this->repository->findOrFail($transactionId);
        $transaction->status_id = 5; // Assuming 5 is the ID for "Received" status
        $transaction->save();
        return $transaction;
    } 
}
