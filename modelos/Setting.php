<?php
class Setting {
    private const STORAGE = __DIR__ . '/../public/storage/settings.json';

    public static function all(): array {
        if (!file_exists(self::STORAGE)) {
            return [];
        }
        $data = json_decode((string)file_get_contents(self::STORAGE), true);
        return is_array($data) ? $data : [];
    }

    public static function get(string $key, $default = null) {
        $settings = self::all();
        return $settings[$key] ?? $default;
    }

    public static function set(string $key, $value): bool {
        $settings = self::all();
        $settings[$key] = $value;
        $dir = dirname(self::STORAGE);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return (bool)file_put_contents(self::STORAGE, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
