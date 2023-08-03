<?php

namespace App\Utilities;

use DateTime;

class Timer
{
    private $begin;
    private $finish;

    public function start()
    {
        $this->begin = new DateTime();
    }

    public function stop()
    {
        $this->finish = new DateTime();
    }

    public function getInterval()
    {
        if ($this->finish === null)
            $this->stop();

        return $this->finish->diff($this->begin)->format('%H:%I:%S.%F');
    }
}