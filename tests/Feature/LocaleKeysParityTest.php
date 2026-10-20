<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

it('ensures every lang/en file has a matching lang/pt_BR file', function () {
    $englishFiles = collect(File::files(lang_path('en')))->map(fn ($file) => $file->getFilename())->sort()->values();
    $portugueseFiles = collect(File::files(lang_path('pt_BR')))->map(fn ($file) => $file->getFilename())->sort()->values();

    expect($portugueseFiles->all())->toBe($englishFiles->all());
});

it('ensures every lang/en file has exactly the same keys as lang/pt_BR', function () {
    $englishFiles = collect(File::files(lang_path('en')))->map(fn ($file) => $file->getFilename())->sort()->values();

    $englishFiles->each(function ($file) {
        $englishKeys = array_keys(Arr::dot(require lang_path('en/' . $file)));
        $portugueseKeys = array_keys(Arr::dot(require lang_path('pt_BR/' . $file)));

        sort($englishKeys);
        sort($portugueseKeys);

        expect($portugueseKeys)->toBe($englishKeys)
            ->and($file)->toBe($file);
    });
});