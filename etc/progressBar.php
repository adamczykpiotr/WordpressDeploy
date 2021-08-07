<?php
/*
 * CLI Progress bar
 *
 * Based on https://snipplr.com/view/29548
 * */

class ProgressBar {

    protected $count = 1;
    protected $startedAt = null;
    protected $ticks = 0;

    /**
     * ProgressBar constructor.
     *
     * @param int $count
     */
    public function __construct($count) {
        $this->count = (int) $count;
    }

    /**
     * Increments progress by one step
     */
    public function tick() {
        $this->ticks++;
        $this->display($this->ticks);
    }

    /**
     * Finishes progress bar
     */
    public function finish() {
        $this->ticks = $this->count;
        $this->display($this->count);
    }

    /**
     * Displays progress bar
     *
     * @param int $progress
     * @param bool|false $index (whether progress is index - counting from 0)
     * @param int $size optional size of the status bar
     */
    public function display($progress, $index = false, $size = 30) {
        if( $this->startedAt === null ) $this->startedAt = time();
        if( $index ) $progress++;

        // if we go over our bound, just ignore it
        if($progress > $this->count) return;

        $now = time();

        $perc = (double)($progress / $this->count);

        $bar = floor($perc * $size);

        $status_bar="\r[";
        $status_bar.=str_repeat("=", $bar);
        if($bar<$size){
            $status_bar.=">";
            $status_bar.=str_repeat(" ", $size-$bar);
        } else {
            $status_bar.="=";
        }

        $disp=number_format($perc*100, 0);

        $status_bar.="] $disp%  $progress/$this->count";

        $rate = ($now-$this->startedAt)/$progress;
        $left = $this->count - $progress;
        $eta = round($rate * $left, 2);

        $elapsed = $now - $this->startedAt;

        $status_bar.= " remaining: ".number_format($eta)." sec.  elapsed: ".number_format($elapsed)." sec.";

        echo "$status_bar  ";

        flush();

        // when done, send a newline
        if($progress == $this->count) {
            echo "\n";
        }
    }
};