<?php

namespace App\Enum;

class CycleEnum
{
    const cycle = [
        'month_price' => '月付',
        'quarter_price' => '季付',
        'half_year_price' => '半年付',
        'year_price' => '年付',
        'three_year_price' => '三年付',
    ];

    // 天数
    const cycleDay = [
        'month_price' => 30,
        'quarter_price' => 91,
        'half_year_price' => 182,
        'year_price' => 365,
        'three_year_price' => 1095,
    ];
}
