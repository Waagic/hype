<?php


namespace App\Service;


class Slugify
{
    public function generate(string $input): string
    {
        $utf8 = array(
            '/[áàâãªä]/u' => 'a',
            '/[ÁÀÂÃÄ]/u' => 'A',
            '/[ÍÌÎÏ]/u' => 'I',
            '/[íìîï]/u' => 'i',
            '/[éèêë]/u' => 'e',
            '/[ÉÈÊË]/u' => 'E',
            '/[óòôõºö]/u' => 'o',
            '/[ÓÒÔÕÖ]/u' => 'O',
            '/[úùûü]/u' => 'u',
            '/[ÚÙÛÜ]/u' => 'U',
            '/ç/' => 'c',
            '/Ç/' => 'C',
            '/ñ/' => 'n',
            '/Ñ/' => 'N'
        );
        $input = preg_replace(array_keys($utf8), array_values($utf8), $input);
        $input = str_replace( array( '?', ',', '.', ':', '!'), '', $input );
        $input = trim($input);
        $input = strtolower(str_replace(" ", "-", $input));
        $input = preg_replace('/[-]{2,}/', '-', $input);
        return $input;
    }
}