<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NoCodeInjection implements Rule
{
    public function passes($attribute, $value)
    {
        $dangerousPattern = '/(<[^>]+>|<\?php|\{\{|\}\}|<script\b[^>]*>(.*?)<\/script>)/i';

        $emojiPattern = '/[\x{1F600}-\x{1F64F}]|' .
            '[\x{1F300}-\x{1F5FF}]|' .
            '[\x{1F680}-\x{1F6FF}]|' .
            '[\x{1F1E0}-\x{1F1FF}]|' .
            '[\x{2600}-\x{26FF}]|' .
            '[\x{2700}-\x{27BF}]/u';

        return !preg_match($dangerousPattern, $value)
            && !preg_match($emojiPattern, $value);
    }


    public function message()
    {
        return 'The :attribute field contains unsupported characters (no scripts, emojis, or unsafe code allowed).';
    }
}
