<?php

namespace App\Enum;

class PayModelEnum
{
    const LOOP = 1;
    const RANDOM = 2;
    const PERIOD = 3;
    const PRICE = 4;

    const options = [
      self::LOOP    => '循环模式',
      self::RANDOM  => '随机模式',
      self::PERIOD  => '时间模式',
      self::PRICE   => '价位模式',
    ];
}
