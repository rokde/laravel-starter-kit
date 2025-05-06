<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class GenerateTranslationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:generate
                            {--path= : Laravel language source path (defaults to Laravel language path)}
                            {--output= : vue-i18n output file (defaults to resources/js/i18n/translations.js)}
                            {--type=typescript : The type of the output (typescript or javascript)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates translation file usable by vue-i18n';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $languagePath = $this->option('path')
            ? base_path($this->option('path'))
            : lang_path();

        if (! is_dir($languagePath)) {
            $this->error("\"{$languagePath}\" does not exist");

            return self::FAILURE;
        }

        $outputFile = $this->option('output') ?
            base_path($this->option('output')) :
            resource_path('js/i18n/translations.js');

        $outputType = $this->option('type');
        if ($outputType === 'typescript') {
            $outputFile = str_replace('.js', '.ts', $outputFile);
        }

        // load app specific language files
        $appLanguagePaths = $this->resolveAppLanguagePaths(app_path());

        // Parse Laravel translations.
        $translations = $this->getTranslations([$languagePath, ...$appLanguagePaths]);

        // Write translation to Vue-i18n file.
        $size = $this->generateVue18nFile($outputFile, $translations, $outputType);

        // Show number of translations per language.
        $this->table(
            ['Language', 'Translations'],
            array_map(
                fn (string $language, array $lines): array => [$language, count($lines)],
                array_keys($translations),
                array_values($translations)
            )
        );

        $this->line("<fg=yellow>{$outputFile}</fg=yellow> generated (<fg=green>{$size} bytes</fg=green>).");

        return self::SUCCESS;
    }

    /**
     * Parse all translation files into a collection.
     *
     * @param  list<string>  $paths
     */
    private function getTranslations(array $paths): array
    {
        return Collection::make($paths)
            ->flatMap(fn ($path): array|false => $this->findTranslationFiles($path))
            ->groupBy(fn ($paths): string => $this->getTranslationLanguage($paths))
            ->map(fn (Collection $files) => $files->flatMap(fn ($file): array => $this->readTranslationFile($file)))
            ->map(fn ($content): array => $this->convertTranslations($content))
            ->all();
    }

    /**
     * Scan the provided path for Laravel JSON and PHP files.
     */
    private function findTranslationFiles(string $path): array|false
    {
        return glob($path.'/{,*/}*.{json,php}', GLOB_BRACE);
    }

    /**
     * Get the translation key based on the provided filename.
     */
    private function getTranslationLanguage(string $filename): string
    {
        return match (pathinfo($filename, PATHINFO_EXTENSION)) {
            'json' => str_replace('.json', '', basename($filename)),
            'php' => basename(dirname($filename)),
        };
    }

    /**
     * Read a JSON or PHP file and parse it into an array.
     *
     * @return array<string,string>
     */
    private function readTranslationFile(string $filename): array
    {
        return match (pathinfo($filename, PATHINFO_EXTENSION)) {
            'json' => json_decode(file_get_contents($filename), true),
            'php' => [basename($filename, '.php') => include ($filename)],
        };
    }

    /**
     * Convert translations into the Vue-i18n format.
     *
     * @return array<string,string|array>
     */
    private function convertTranslations(Collection $lines): array
    {
        return $lines
            ->mapWithKeys(fn ($translation, $key) => [
                $this->convertTranslation($key) => $this->convertTranslation($translation),
            ])
            ->all();
    }

    /**
     * Converts a single translation line.
     */
    private function convertTranslation(string|array $content): string|array
    {
        // Handle nested translations
        if (is_array($content)) {
            return array_combine(
                array_keys($content),
                array_map(fn ($value): string|array => $this->convertTranslation($value), $content)
            );
        }

        return Str::of($content)
            ->pipe(fn ($line): string => $this->transformPluralization($line))
            ->pipe(fn ($line): string => $this->transformColonsToBraces($line))
            ->pipe(fn ($line): string => $this->removeEscapeCharacter($line))
            ->value();
    }

    /**
     * Convert Laravel into Vue18n pluralization style.
     */
    private function transformPluralization(string $line): string
    {
        return preg_replace_callback(
            "/\{0\}\s(.*)\|\{1\}(.*)\|\[2,\*\](.*)/",
            fn ($matches): string => "{$matches[1]}|{$matches[2]}|{$matches[3]}",
            $line
        );
    }

    /**
     * Turn Laravel style ":link" into vue-i18n style "{link}".
     */
    private function transformColonsToBraces(string $line): string
    {
        return preg_replace_callback(
            '/(?<!mailto|tel|'.preg_quote('!', '/')."):\w+/",
            fn ($matches): string => '{'.mb_substr($matches[0], 1).'}',
            $line
        );
    }

    /**
     * Remove escape characters.
     */
    private function removeEscapeCharacter(string $line): string
    {
        return preg_replace_callback(
            '/'.preg_quote('!', '/')."(:\w+)/",
            fn ($matches): string => '{'.mb_substr($matches[0], 1).'}',
            $line
        );
    }

    /**
     * Writes translations to a JSON file.
     *
     * @return int|false filesize in bytes
     */
    private function generateVue18nFile(string $filename, array $translations, string $outputType): int|false
    {
        (new Filesystem)->ensureDirectoryExists(dirname($filename));

        return file_put_contents(
            $filename,
            $this->convertTranslationsToVue18n($translations, $outputType)
        );
    }

    /**
     * Convert translation array to Vue-i18n JSON file.
     */
    private function convertTranslationsToVue18n(array $translations, string $outputType): string
    {
        $json = json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if ($outputType === 'typescript') {
            $output = 'type TranslationValue = string | { [key: string]: string | TranslationValue } | [];'.PHP_EOL;
            $output .= 'type Translations = { [language: string]: { [key: string]: TranslationValue; }; };'.PHP_EOL.PHP_EOL;

            $output .= "const translations: Translations = {$json}".PHP_EOL;

            return $output.('export default translations;'.PHP_EOL);
        }

        return "export default {$json}".PHP_EOL;
    }

    /**
     * @return array<int, string>
     */
    private function resolveAppLanguagePaths(string $app_path, string $languageFoldername = 'Lang'): array
    {
        return glob($app_path.'/**/**/'.$languageFoldername, GLOB_ONLYDIR);
    }
}
