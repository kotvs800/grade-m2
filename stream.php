<?php

function getNameTempFile(): string
{
    $tmpfile = tmpfile();
    $meta = stream_get_meta_data($tmpfile);
    fclose($tmpfile);

    return $meta['uri'];
}

function getContent(): string
{
    $handle = fopen('file.txt', 'r');
    $content = stream_get_contents($handle);
    fclose($handle);

    return $content;
}

function copyFile(): void
{
    $handle = fopen('file.txt', 'r');
    $newHandle = fopen('newFile.txt', 'w');

    stream_copy_to_stream($handle, $newHandle);

    fclose($handle);
    fclose($newHandle);
}

function copyHtmlPageToFile(): void
{
    $options = [
        'https' => [
            'method' => 'GET',
            'header' => "Cookie: app_city=1\r\n"
        ]
    ];

    $context = stream_context_create($options);

    $handle = fopen('https://hyperauto.ru', 'r', false, $context);
    $fileHandle = fopen('newFile.txt', 'w');

    stream_copy_to_stream($handle, $fileHandle);

    fclose($handle);
    fclose($fileHandle);
}

function printWrappers(): void
{
    print_r(stream_get_wrappers());

    /*
     Array
    (
        [0] => https
        [1] => ftps
        [2] => compress.zlib
        [3] => compress.bzip2
        [4] => php
        [5] => file
        [6] => glob
        [7] => data
        [8] => http
        [9] => ftp
        [10] => phar
        [11] => zip
    )
    */
}

function unregisterWrapper(): void
{
    if (in_array('https', stream_get_wrappers())) {
        stream_wrapper_unregister('https');
    }

    try {
        $stream = fopen('https://drom.ru', 'r');
        $content = stream_get_contents($stream);
    } catch (Throwable $throwable) {
        // Fatal error: Uncaught TypeError: stream_get_contents(): Argument #1 ($stream) must be of type resource, bool given
    }

    // PHP Warning:  fopen(): Unable to find the wrapper "https" - did you forget to enable it when you configured PHP?
}

function changeHttpsWrapper(): void
{
    if (in_array('https', stream_get_wrappers())) {
        stream_wrapper_unregister('https');
    }

    stream_wrapper_register('https', 'StreamWrapper', STREAM_IS_URL);

    $stream = fopen('https://drom.ru', 'r');
    $content = stream_get_contents($stream);
}

class StreamWrapper extends AbstractStreamWrapper
{
    // Some realization
}

abstract class AbstractStreamWrapper implements StreamWrapperInterface
{
    /** @var resource */
    public $context;
}

interface StreamWrapperInterface
{
    public function __construct();
    public function dir_closedir(): bool;
    public function dir_opendir(string $path, int $options): bool;
    public function dir_readdir(): string;
    public function dir_rewinddir(): bool;
    public function mkdir(string $path, int $mode, int $options): bool;
    public function rename(string $path_from, string $path_to): bool;
    public function rmdir(string $path, int $options): bool;
    /**
     * @return resource
     */
    public function stream_cast(int $cast_as);
    public function stream_close(): void;
    public function stream_eof(): bool;
    public function stream_flush(): bool;
    public function stream_lock(int $operation): bool;
    public function stream_metadata(string $path, int $option, mixed $value): bool;
    public function stream_open(
        string $path,
        string $mode,
        int $options,
        ?string &$opened_path
    ): bool;
    public function stream_read(int $count): string|false;
    public function stream_seek(int $offset, int $whence = SEEK_SET): bool;
    public function stream_set_option(int $option, int $arg1, int $arg2): bool;
    public function stream_stat(): array|false;
    public function stream_tell(): int;
    public function stream_truncate(int $new_size): bool;
    public function stream_write(string $data): int;
    public function unlink(string $path): bool;
    public function url_stat(string $path, int $flags): array|false;
    public function __destruct();
}
