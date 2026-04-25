<?php
namespace Core\Currency;

use Core\Database\Database;

class CurrencyManager
{
    private Database $db;
    private array $currencies = [];
    private array $rates = [];

    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->loadCurrencies();
    }

    private function loadCurrencies(): void
    {
        try {
            $this->currencies = $this->db->fetchAll("SELECT * FROM currencies WHERE is_active = 1");
            $this->rates = $this->db->fetchAll(
                "SELECT * FROM exchange_rates WHERE effective_date <= CURDATE() ORDER BY effective_date DESC"
            );
        } catch (\Exception $e) {
            $this->currencies = [];
            $this->rates = [];
        }
    }

    public function getCurrencies(): array
    {
        return $this->currencies;
    }

    public function getCurrency(string $code): ?array
    {
        foreach ($this->currencies as $currency) {
            if ($currency['code'] === $code) {
                return $currency;
            }
        }
        return null;
    }

    public function convert(float $amount, string $from, string $to): float
    {
        if ($from === $to) return $amount;

        $rate = $this->getRate($from, $to);
        return round($amount * $rate, 4);
    }

    public function getRate(string $from, string $to): float
    {
        foreach ($this->rates as $rate) {
            if ($rate['from_currency'] === $from && $rate['to_currency'] === $to) {
                return (float) $rate['rate'];
            }
        }
        // Try inverse
        foreach ($this->rates as $rate) {
            if ($rate['from_currency'] === $to && $rate['to_currency'] === $from) {
                return 1.0 / (float) $rate['rate'];
            }
        }
        return 1.0;
    }

    public function format(float $amount, string $currencyCode): string
    {
        $currency = $this->getCurrency($currencyCode);
        if (!$currency) return number_format($amount, 2);

        $symbol = $currency['symbol'];
        $position = $currency['symbol_position'] ?? 'before';
        $formatted = number_format($amount, (int)($currency['decimal_places'] ?? 2));

        return $position === 'before'
            ? $symbol . ' ' . $formatted
            : $formatted . ' ' . $symbol;
    }

    public function updateRate(string $from, string $to, float $rate): void
    {
        $existing = $this->db->fetch(
            "SELECT id FROM exchange_rates WHERE from_currency = ? AND to_currency = ? AND effective_date = CURDATE()",
            [$from, $to]
        );

        if ($existing) {
            $this->db->update('exchange_rates', ['rate' => $rate], 'id = ?', [$existing['id']]);
        } else {
            $this->db->insert('exchange_rates', [
                'from_currency' => $from,
                'to_currency' => $to,
                'rate' => $rate,
                'effective_date' => date('Y-m-d'),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
