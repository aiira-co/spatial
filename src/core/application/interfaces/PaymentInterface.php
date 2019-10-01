<?php
namespace Core\Application\Interfaces;

interface PaymentInterface
{
    public function checkPayment(int $userId): string;
}
