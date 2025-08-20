<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NoCodeInjection implements Rule
{
    public function passes($attribute, $value)
    {
        // Block <script>, PHP tags, and Blade expressions
        $dangerousPattern = '/(<\s*script.*?>.*?<\s*\/\s*script\s*>|<\?php|\{\{|\}\})/is';

        $codeLikePattern = '/
                (SELECT\s+\*\s+FROM\s+\w+)          |  # SQL SELECT 
                (INSERT\s+INTO\s+\w+)               |  # SQL INSERT
                (DELETE\s+FROM\s+\w+)               |  # SQL DELETE
                (UPDATE\s+\w+\s+SET)                |  # SQL UPDATE
                (DROP\s+TABLE\s+\w+)                |  # SQL DROP
                (= *getRequestString\s*\()          |  # JavaScript-style pattern
                (txtSQL\s*=\s*".*?")                |  # JS assignment
                (\b(alert|prompt|confirm)\s*\()     |  # JS functions
                (<script.*?>.*?<\/script>)          |  # Script tags
                (<\?php)                            |  # PHP code
                (\{\{|\}\})                            # Blade syntax
            /ix';


        // Optional: block emojis
        $emojiPattern = '/[\x{1F600}-\x{1F64F}]|' .
            '[\x{1F300}-\x{1F5FF}]|' .
            '[\x{1F680}-\x{1F6FF}]|' .
            '[\x{1F1E0}-\x{1F1FF}]|' .
            '[\x{2600}-\x{26FF}]|' .
            '[\x{2700}-\x{27BF}]/u';

        return !preg_match($dangerousPattern, $value)
            && !preg_match($codeLikePattern, $value)
            && !preg_match($emojiPattern, $value);
    }

    public function message()
    {
        return 'The :attribute field contains unsupported code (like scripts or PHP).';
    }
}

