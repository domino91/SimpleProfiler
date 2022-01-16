<?php
namespace domino91\SimpleProfiler;

class SimpleProfiler
{
    private static $timer = [];

    public static function start($noisy = false)
    {
        self::$timer = [];

        self::$timer[] = [
            'stepName' => 'start',
            'time'     => microtime(true)
        ];

       if ($noisy) {
            printf(
                "%s [%d]\n",
                "start"
                microtime(true)
            );

       }
    }

    /**
     * Use before testing code area
    */
    public static function step(string $stepName, $noisy = false)
    {
        self::$timer[] = [
            'stepName' => $stepName,
            'time'     => microtime(true)
        ];

        if ($noisy) {
            printf(
                "%s [%d]\n",
                $stepName,
                microtime(true)
            );

       }

    }

    public static function stop($noisy = false)
    {
        self::$timer[] = [
            'stepName' => 'stop',
            'time'     => microtime(true)
        ];

       if ($noisy) {
            printf(
                "%s [%d]\n",
                "stop",
                microtime(true)
            );

       }

    }

    public static function saveToFile(string $filename)
    {
        $result = self::calculate();

        $content = '';

        foreach ($result as $timer) {
            $content .= sprintf(
                "%s [%d:%d:%d]\n",
                $timer['stepName'],
                $timer['hours'],
                $timer['minutes'],
                $timer['seconds']
            );
        }

        file_put_contents($filename, $content);
    }

    private static function calculate(): array
    {
        $result = [];

        if (count(self::$timer) < 2) {
            throw new InvalidArgumentException('Start and stop is needed!');
        }

        $previousTime = null;

        foreach (self::$timer as $key => $timer) {
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
                'seconds'  => $seconds
            ];
        }

        return $result;
    }
}
