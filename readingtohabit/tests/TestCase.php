<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function makeAlphaStringIncludingKana($alpha_min_len, $str_len) {
        do {
            $alpha_num = rand($alpha_min_len, $str_len - 1);
            $alpha = str_random($alpha_num);

            $kana_num = $str_len - $alpha_num;
            $str_to_kana = str_random($kana_num);
            $kana = mb_convert_kana($str_to_kana, "A");
            $alpha_string_including_kana = str_shuffle($alpha.$kana);
        } while(mb_strlen($alpha_string_including_kana) > $str_len);

        return $alpha_string_including_kana;
    }
    
    public function makeAlphaStringIncludingSign($alpha_min_len, $str_len) {
        $alpha_num = rand($alpha_min_len, $str_len - 1);
        $alpha = str_random($alpha_num);

        $sign_num = $str_len - $alpha_num;
        $collection_sybl_str = implode(array_merge(range('!','/'), range(':','@'), range('{','~')));
        $sybl = substr(str_shuffle($collection_sybl_str), 0, $sign_num);

        return str_shuffle($alpha.$sybl);
    }
}
