export function localeNumber(number: number | string | null | undefined): string {
    if (!number) return '';

    const value = Number(number);

    const options: { [key: string]: Intl.NumberFormatOptions } = {};

    options.fallback = { style: 'decimal' };

    return value.toLocaleString(window.locale, options[window.locale] ?? options.fallback);
}

export function localeMoney(number: number | string | null | undefined, currency: string): string {
    if (!number) return '';

    const value = Number(number);

    const options: { [key: string]: Intl.NumberFormatOptions } = {};

    options.fallback = { style: 'currency', currencyDisplay: 'symbol', currency };

    return value.toLocaleString(window.locale, options[window.locale] ?? options.fallback);
}

export function localePercent(number: number | string | null | undefined, fractionDigits: number | undefined = undefined): string {
    if (!number) return '';

    const value = Number(number);

    const options: { [key: string]: Intl.NumberFormatOptions } = {};

    options.fallback = {
        style: 'percent',
        roundingPriority: 'morePrecision',
        minimumFractionDigits: fractionDigits,
        minimumSignificantDigits: fractionDigits,
        minimumIntegerDigits: fractionDigits,
        maximumFractionDigits: fractionDigits,
        maximumSignificantDigits: fractionDigits,
    };

    return value.toLocaleString(window.locale, options[window.locale] ?? options.fallback);
}

export function localeNumberUnit(number: number | string | null | undefined, unit: string): string {
    const value = localeNumber(number);
    if (!value) return '';

    return `${value} ${unit}}`;
}
