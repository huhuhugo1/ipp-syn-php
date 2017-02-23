<?php 
class Regex {
    public $regex = "";

    function get() {
        return $this->regex;
    }

    function __construct($ipp_regex) {
        $perl_regex = "";
        $negation = "";

        $PCRE_metachars = array("^", "$", "?", "[", "]", "{", "}", "\\", "-");
        $common_metachars = array("|", "*", "+", "(", ")");

        $regex_length = mb_strlen($ipp_regex, "UTF-8");

        for ($i = 0; $i < $regex_length; $i++) {
            $char = mb_substr($ipp_regex, $i, 1, "UTF-8");
            
            if (in_array($char, $PCRE_metachars))
                $perl_regex .= "\\" . $char;
            else if (in_array($char, $common_metachars))
                $perl_regex .= $char;
            else if ($char === ".")
                ; //nothing, concatenation operator
            else if ($char === "%") {
                $i++;
                switch ($char = mb_substr($ipp_regex, $i, 1, "UTF-8")) {
                    case "s": $perl_regex .= "[ \\t\\n\\r\\f\\v]"; break;
                    case "a": $perl_regex .= "."; break;
                    case "d": $perl_regex .= "[0-9]"; break;
                    case "l": $perl_regex .= "[a-z]"; break;
                    case "L": $perl_regex .= "[A-Z]"; break;
                    case "w": $perl_regex .= "[a-zA-Z]"; break;
                    case "W": $perl_regex .= "[0-9a-zA-Z]"; break;
                    case "t": $perl_regex .= "\\t"; break;
                    case "n": $perl_regex .= "\\n"; break;
                    case ".": $perl_regex .= "\\."; break;
                    case "|": $perl_regex .= "\\|"; break;
                    case "!": $perl_regex .= "!"; break;
                    case "*": $perl_regex .= "\\*"; break;
                    case "+": $perl_regex .= " \\+"; break;
                    case "(": $perl_regex .= "\\("; break;
                    case ")": $perl_regex .= "\\)"; break;
                    case "%": $perl_regex .= "%"; break;
                }
            } else if ($char === "!") {
                ;
            } else if (ord($char) >= 32) 
                $perl_regex .= $char;
            else {
                $this->regex = "";
                return false;
            }
        }
        $this->regex = $perl_regex;
        return true;
    }
}
?>
