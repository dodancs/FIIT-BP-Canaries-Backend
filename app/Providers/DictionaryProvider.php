<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class DictionaryProvider extends ServiceProvider {

    private $millPasswords;
    private $numPasswords;
    private $englishWords;
    private $numWords;

    public function boot() {
        $this->millPasswords = file(__DIR__ . '/../../resources/top-mil-passwords.txt', FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
        $this->numPasswords = count($this->millPasswords);
        $this->englishWords = file(__DIR__ . '/../../resources/words-only.txt', FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
        $this->numWords = count($this->englishWords);

        Config::set('milPasswords', $this->millPasswords);
        Config::set('numPasswords', $this->numPasswords);
        Config::set('englishWords', $this->englishWords);
        Config::set('numWords', $this->numWords);

    }

}