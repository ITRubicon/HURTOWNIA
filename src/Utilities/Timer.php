<?php

namespace App\Utilities;

class Timer
{
    private $begin;
    private $finish;

    public function start()
    {
        $this->reset();
        $this->begin = microtime(true);
    }

    public function stop()
    {
        $this->finish = microtime(true);
    }

    public function reset()
    {
        $this->begin = null;
        $this->finish = null;
    }

    public function getInterval()
    {
        if ($this->finish === null)
            $this->stop();

        return number_format($this->finish - $this->begin, 4);
    }
}