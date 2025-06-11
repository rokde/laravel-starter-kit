<?php

declare(strict_types=1);

namespace App\Data;

use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Support\Str;

class Timezones
{
    /**
     * @return array<string, array{value: string, label: string}>
     */
    public function selectableList(): array
    {
        $regions = [
            'Africa' => DateTimeZone::AFRICA,
            'Americas' => DateTimeZone::AMERICA,
            'Antartica' => DateTimeZone::ANTARCTICA,
            'Arctic' => DateTimeZone::ARCTIC,
            'Asia' => DateTimeZone::ASIA,
            'Atlantic' => DateTimeZone::ATLANTIC,
            'Australia' => DateTimeZone::AUSTRALIA,
            'Europe' => DateTimeZone::EUROPE,
            'Indian Ocean' => DateTimeZone::INDIAN,
            'Pacific' => DateTimeZone::PACIFIC,
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
                    'label' => "({$prettyOffset}) {$label}",
                ];
            }
        }

        return $result;
    }

    public static function identifiers(): array
    {
        return DateTimeZone::listIdentifiers();
    }
}
