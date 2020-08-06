<?php

namespace Moka;

/**
 * Class Funnel
 * @author Lucifer from Moka
 */
class Funnel
{
    public $capacity        = ''; # 漏斗容量
    public $leaking_rate    = ''; # 漏嘴流水速率 增加空间的速率
    public $left_quota      = ''; # 漏斗剩余空间
    public $leaking_ts      = ''; # 上次漏水时间

    public function __construct($capacity, $leaking_rate)
    {
        $this->capacity     = $capacity;        // 漏斗容量
        $this->leaking_rate = $leaking_rate;    // 漏斗流水速率
        $this->left_quota   = $capacity;        // 漏斗剩余空间
        $this->leaking_ts   = time();           // 上次漏水时间
    }

    public function watering($quota)
    {
        $this->makeSpace(); //漏水操作
        if ($this->left_quota >= $quota) {
            $this->left_quota -= $quota;
            return true;
        }
        return false;
    }

    public function makeSpace()
    {
        $now = time();
        $delta_ts = $now - $this->leaking_ts;           //距离上一次漏水过去了多久
        $delta_quota = $delta_ts * $this->leaking_rate; //可腾出的空间
        if ($delta_quota < 1) {
            return;
        }
        $this->left_quota += $delta_quota;   //增加剩余空间
        $this->leaking_ts = time();          //记录漏水时间
        if ($this->left_quota > $this->capacity) {
            $this->left_quota = $this->capacity;
        }
    }
}
