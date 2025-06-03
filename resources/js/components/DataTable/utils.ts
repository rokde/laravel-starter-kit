// eslint-disable-next-line @typescript-eslint/no-unsafe-function-type
export function createDebounce(): Function {
    let timeout: ReturnType<typeof setTimeout> | undefined = undefined;
    // eslint-disable-next-line @typescript-eslint/no-unsafe-function-type
    return function (callback: Function, delayMs: number) {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            callback();
        }, delayMs || 500);
    };
}
