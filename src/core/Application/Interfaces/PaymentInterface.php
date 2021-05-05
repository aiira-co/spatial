<?php

namespace Core\Application\Interfaces;

interface PaymentInterface
{
    public function makePayment(array $data): array;

    public function checkTransactionStatus(string $transactionId): array;
}
