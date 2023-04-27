<?php

namespace App\Admin\Controllers;

use App\Admin\Metrics\Examples\Flow;
use App\Admin\Metrics\Examples\IncomeFlow;
use App\Admin\Metrics\Examples\NewUsers;
use App\Admin\Metrics\Examples\PaymentFlow;
use App\Admin\Metrics\Examples\Orders;
use App\Admin\Metrics\Examples\ProductOrders;
use App\Admin\Metrics\Examples\Settlement;
use App\Http\Controllers\Controller;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->header('汇总')
            ->body(function (Row $row) {
                $row->column(3, new NewUsers());
                $row->column(3, new Flow());
                $row->column(3, new Orders());
                $row->column(3, new Settlement());
            })
            ->body(function (Row $row) {
                $row->column(6, new IncomeFlow());
                $row->column(6, new ProductOrders());
            })
            ->body(function (Row $row) {
                $row->column(12, new PaymentFlow());
            });
    }
}
