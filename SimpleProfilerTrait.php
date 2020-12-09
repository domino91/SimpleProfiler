<?php
namespace domino91\SimpleProfiler;

trait SimpleProfilerTrait
{
    private $timer = [];

    public function start()
    {
        $this->timer = [];

        $this->timer[] = [
            'stepName' => 'start',
            'time'     => microtime(true),
        ];
    }

    public function step(string $stepName)
    {
        $this->timer[] = [
            'stepName' => $stepName,
            'time'     => microtime(true),
        ];
    }

    public function stop()
    {
        $this->timer[] = [
            'stepName' => 'stop',
            'time'     => microtime(true),
        ];
    }

    public function saveToFile(string $filename)
    {
        $result = $this->calculate();

        $content = '';

        foreach ($result as $timer) {
            $content .= sprintf(
                "%s [%d:%d:%d]\n",
                $timer['stepName'],
                $timer['hours'],
                $timer['minutes'],
                $timer['seconds'],
            );
        }

        file_put_contents($filename, $content);
    }

    private function calculate(): array
    {
        $result = [];

        if (count($this->timer) < 2) {
            throw new InvalidArgumentException('Start and stop is needed!');
        }

        $previousTime = null;

        foreach ($this->timer as $key => $timer) {
            if ($key == 0) {
                $previousTime = $timer['time'];
                continue;
            }

            $duration = $timer['time'] - $previousTime;

            $hours   = (int)($duration / 60 / 60);
            $minutes = (int)($duration / 60) - $hours * 60;
            $seconds = (int)$duration - $hours * 60 * 60 - $minutes * 60;

            $previousTime = $timer['time'];

            $result[] = [
                'stepName' => $timer['stepName'],
                'hours'    => $hours,
                'minutes'  => $minutes,
                'seconds'  => $seconds,
            ];
        }

        return $result;
    }
}
