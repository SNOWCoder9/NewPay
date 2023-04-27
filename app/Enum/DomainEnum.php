<?php
namespace App\Enum;

class DomainEnum
{
    const REVIEW = 0;
    const FAIL = 1;
    const SUCCESS = 2;
    const DISABLED = 3;

    const list = [
        self::REVIEW     => '审核中',
        self::FAIL       => '失败',
        self::SUCCESS    => '通过',
        self::DISABLED   => '禁用',
    ];
}
