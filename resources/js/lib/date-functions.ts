import { CalendarDate } from '@internationalized/date';

/**
 * returns the localized date
 */
export function localeDate(date: CalendarDate | string | null | undefined, type: 'date' | 'time' | 'datetime' | 'ISO8601' = 'date'): string {
    if (!date) return '';

    if (type === 'ISO8601') {
        return new Date(Date.parse(date)).toISOString();
    }

    const options: { [key: string]: Intl.DateTimeFormatOptions } = {};

    options.fallback = {
        ...(type !== 'time' ? { year: 'numeric', month: '2-digit', day: '2-digit' } : {}),
        ...(type !== 'date' ? { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true } : {}),
    };

    options['de'] = {
        ...(type !== 'time' ? { year: 'numeric', month: '2-digit', day: '2-digit' } : {}),
        ...(type !== 'date' ? { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false } : {}),
    };

    if (type === 'time') {
        return new Date(Date.parse(date)).toLocaleTimeString(window.locale, options[window.locale] ?? options.fallback);
    }

    return new Date(Date.parse(date)).toLocaleDateString(window.locale, options[window.locale] ?? options.fallback);
}

export function timeAgo(date: Date | string): { interval: number; unit: string } {
    let _date: Date;

    if (typeof date === 'string') {
        _date = new Date(Date.parse(date));
    } else {
        _date = date;
    }

    const seconds: number = Math.floor((new Date().getTime() - _date.getTime()) / 1000);

    const intervals: Record<string, number> = {
        year: 31536000,
        month: 2628000,
        day: 86400,
        hour: 3600,
        minute: 60,
    };

    for (const [unit, secondsInUnit] of Object.entries(intervals)) {
        const interval: number = Math.floor(seconds / secondsInUnit);
        if (interval > 1) {
            return { interval, unit: `${unit}s` };
        } else if (interval === 1) {
            return { interval, unit };
        }
    }

    return { interval: 0, unit: 'seconds' };
}

export function humanReadable(date: Date | string): { interval: number; unit: string; direction: 'ago' | 'from now' | 'now' } {
    let _date: Date;

    if (typeof date === 'string') {
        _date = new Date(Date.parse(date));
    } else {
        _date = date;
    }

    const now: number = new Date().getTime();
    const diffSeconds: number = Math.floor((now - _date.getTime()) / 1000);

    let direction: 'ago' | 'from now' | 'now';
    let seconds: number;

    if (diffSeconds > 0) {
        direction = 'ago';
        seconds = diffSeconds;
    } else if (diffSeconds < 0) {
        direction = 'from now';
        seconds = Math.abs(diffSeconds); // Betrag für zukünftige Zeitpunkte
    } else {
        return { interval: 0, unit: 'seconds', direction: 'now' };
    }

    const intervals: Record<string, number> = {
        year: 31536000,
        month: 2628000,
        day: 86400,
        hour: 3600,
        minute: 60,
    };

    for (const [unit, secondsInUnit] of Object.entries(intervals)) {
        const interval: number = Math.floor(seconds / secondsInUnit);
        if (interval > 1) {
            return { interval, unit: `${unit}s`, direction };
        } else if (interval === 1) {
            return { interval, unit, direction };
        }
    }

    return { interval: seconds, unit: 'second', direction }; // Rückgabe "seconds" statt "0 seconds" für kleine Differenzen
}
