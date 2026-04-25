<?php
namespace Core\Controllers\Api;

use Core\Controllers\BaseController;

class CurrencyApiController extends BaseController
{
    public function list(): void
    {
        $currencies = $this->app->getCurrency()->getCurrencies();
        $this->json(['currencies' => $currencies]);
    }

    public function convert(): void
    {
        $amount = (float) $this->input('amount', 0);
        $from = $this->input('from', 'EGP');
        $to = $this->input('to', 'USD');

        $converted = $this->app->getCurrency()->convert($amount, $from, $to);
        $formatted = $this->app->getCurrency()->format($converted, $to);

        $this->json([
            'success' => true,
            'original' => $amount,
            'from' => $from,
            'to' => $to,
            'converted' => $converted,
            'formatted' => $formatted,
            'rate' => $this->app->getCurrency()->getRate($from, $to),
        ]);
    }
}
