<?php

namespace App\Services;

use App\Enum\PayModelEnum;
use App\Models\Payment;
use Illuminate\Support\Facades\Cache;

class PaymentService
{
    public $method;
    protected $class;
    protected $config = [];
    protected $payment;

    public function __construct($payment_id)
    {
        $payment = Payment::query()->where('id', $payment_id)->first();
        if (!$payment) abort(500, 'payment is not found');
        $this->method = $payment->payment;
        $this->class = '\\App\\Payments\\' . $this->method;
        if (!class_exists($this->class)) abort(500, 'payment is not found');
        if ($payment = Payment::query()->where('id', $payment_id)->first()) {
            $this->config = $payment->config;
        };

        $this->payment = new $this->class($this->config);
    }
    public function purchase($order)
    {
        if (isset($order->notify_method)) {
            $order->notify_url = getConfig('api_host') . '/api/v1/site/buy_shop/' . $order->payment_id;
        } else {
            $order->notify_url = getConfig('api_host') . '/api/v1/notify/' . $order->token . '/' . $order->payment_id;
        }

        return $this->payment->purchase($order);
    }

    public function form()
    {
        $form = $this->payment->config();
        $keys = array_keys($form);
        foreach ($keys as $key) {
            if (isset($this->config[$key])) $form[$key]['value'] = $this->config[$key];
        }

        return $form;
    }

    public function notify($request)
    {
        return $this->payment->notify($request);
    }

    public function refund($order)
    {
        return $this->payment->refund($order);
    }

    public function config()
    {
        return $this->config;
    }

    /**
     * @param array $payment_ids
     * @param $price
     * @return mixed
     */
    public static function getPayment(array $payment_ids, float $price = 0)
    {
        $payments = Payment::query()
            ->whereIn('id', $payment_ids)
            ->get()
            ->toArray();

        $payment_ids = array_column($payments, 'id');

        $pay_model = getConfig('pay_model_setting', 1);
        if ($pay_model == PayModelEnum::LOOP) {
            $last_id = Cache::get('last_payment_id', -1);
            if ($last_id == -1 || $last_id >= (count($payment_ids) - 1)) {
                $payment_id = 0;
            } else {
                $payment_id = $last_id + 1;
            }
        } elseif ($pay_model == PayModelEnum::RANDOM) {
            $payment_id = array_rand($payment_ids);
        } elseif ($pay_model == PayModelEnum::PERIOD) {
            $ids = [];
            foreach ($payments as $payment) {
                if ($payment['period'] && strpos($payment['period'], '-')) {
                    $period_gap = explode('-', $payment['period']);
                    $now_time = date('G');
                    if ($period_gap[0] <= $now_time && $period_gap[1] >= $now_time) {
                        array_push($ids, $payment['id']);
                    }
                }
            }
            $payment_id = array_search($ids[array_rand($ids)], $payment_ids);
        } elseif ($pay_model == PayModelEnum::PRICE) {
            $ids = [];
            foreach ($payments as $payment) {
                if ($payment['price'] && strpos($payment['price'], '-')) {
                    $price_gap = explode('-', $payment['price']);
                    if ($price_gap[0] <= $price && $price_gap[1] >= $price) {
                        array_push($ids, $payment['id']);
                    }
                }
            }
            $payment_id = array_search($ids[array_rand($ids)], $payment_ids);
        }

        Cache::put('last_payment_id', $payment_id, now()->addHour());

        return $payment_ids[$payment_id];
    }
}
