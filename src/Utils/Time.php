<?php

namespace Rumur\WordPress\Notice\Utils;

class Time
{
    /**
     * @uses \current_datetime
     * @return \DateTimeInterface
     */
    public static function now(): \DateTimeInterface
    {
        return \current_datetime();
    }

    /**
     * Makes sure time has been converted to a timestamp.
     *
     * @param \DateTimeInterface|int|string $time The time that need to be converted.
     *
     * @return int
     */
    public static function toTimestamp($time): int
    {
        $timestamp = 0;

        if (\is_string($time)) {
            $timestamp = \strtotime($time, static::now()->getTimestamp());
        }

        if (\is_int($time)) {
            $timestamp = $time;
        }

        if ($time instanceof \DateTimeInterface) {
            $timestamp = $time->getTimestamp();
        }

        if (! $timestamp) {
            throw new \InvalidArgumentException(
                sprintf('Seems `$time: %s` has wrong format or could not be converted to a timestamp.', $time)
            );
        }

        return $timestamp;
    }
}
