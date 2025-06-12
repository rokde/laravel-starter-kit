<?php

declare(strict_types=1);

namespace App\Data;

use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Support\Str;

class Timezones
{
    public static function identifiers(): array
    {
        return DateTimeZone::listIdentifiers();
    }

    /**
     * @return array<string, array{value: string, label: string}>
     */
    public function selectableList(): array
    {
        $regions = [
            __('timezones.Africa') => DateTimeZone::AFRICA,
            __('timezones.Americas') => DateTimeZone::AMERICA,
            __('timezones.Antartica') => DateTimeZone::ANTARCTICA,
            __('timezones.Arctic') => DateTimeZone::ARCTIC,
            __('timezones.Asia') => DateTimeZone::ASIA,
            __('timezones.Atlantic') => DateTimeZone::ATLANTIC,
            __('timezones.Australia') => DateTimeZone::AUSTRALIA,
            __('timezones.Europe') => DateTimeZone::EUROPE,
            __('timezones.Indian Ocean') => DateTimeZone::INDIAN,
            __('timezones.Pacific') => DateTimeZone::PACIFIC,
        ];

        $regionList = [];

        foreach ($regions as $name => $region) {
            $regionList[$name] = DateTimeZone::listIdentifiers($region);
        }

        $timezoneOffsets = [];
        foreach ($regionList as $name => $timezones) {
            foreach ($timezones as $timezone) {
                $tz = new DateTimeZone($timezone);
                $timezoneOffsets[$name][$timezone] = $tz->getOffset(new DateTimeImmutable);
            }

            asort($timezoneOffsets[$name]);
        }

        // sort timezone by offset
        $result = [];
        foreach ($timezoneOffsets as $name => $timezones) {
            foreach ($timezones as $timezone => $offset) {
                $offsetPrefix = $offset < 0 ? '-' : '+';
                $offsetFormatted = gmdate('H:i', abs($offset));
                $prettyOffset = "{$offsetPrefix}{$offsetFormatted}";

                $label = $timezone;
                if (mb_substr_count($timezone, '/') > 0) {
                    [,$label] = explode('/', $timezone, 2);
                }
                $label = Str::of($label)->replace('_', ' ')->value();

                $result[$name][] = [
                    'value' => $timezone,
                    'label' => $label,
                    'offset' => $prettyOffset,
                ];
            }
        }

        return $result;
    }
}
