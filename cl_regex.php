<?php 
class Regex {
    public $ipp_regex;
    public $pcre_regex;

    function __construct($ipp_regex) {
        $this->ipp_regex = $ipp_regex;
        $this->pcre_regex = "";

        $negation = "";

        $PCRE_metachars = array("^", "$", "?", "[", "]", "{", "}", "\\", "-");
        $common_metachars = array("|", "*", "+", "(", ")");

        $regex_length = mb_strlen($ipp_regex, "UTF-8");

        for ($i = 0; $i < $regex_length; $i++) {
            $char = mb_substr($ipp_regex, $i, 1, "UTF-8");
            
            if (in_array($char, $PCRE_metachars))
                $this->pcre_regex .= "\\" . $char;
            else if (in_array($char, $common_metachars))
                $this->pcre_regex .= $char;
            else if ($char === ".")
                ; //nothing, concatenation operator
            else if ($char === "%") {
                $i++;
                switch ($char = mb_substr($ipp_regex, $i, 1, "UTF-8")) {
                    case "s": $this->pcre_regex .= "[ \\t\\n\\r\\f\\v]"; break;
                    case "a": $this->pcre_regex .= "."; break;
                    case "d": $this->pcre_regex .= "[0-9]"; break;
                    case "l": $this->pcre_regex .= "[a-z]"; break;
                    case "L": $this->pcre_regex .= "[A-Z]"; break;
                    case "w": $this->pcre_regex .= "[a-zA-Z]"; break;
                    case "W": $this->pcre_regex .= "[0-9a-zA-Z]"; break;
                    case "t": $this->pcre_regex .= "\\t"; break;
                    case "n": $this->pcre_regex .= "\\n"; break;
                    case ".": $this->pcre_regex .= "\\."; break;
                    case "|": $this->pcre_regex .= "\\|"; break;
                    case "!": $this->pcre_regex .= "!"; break;
                    case "*": $this->pcre_regex .= "\\*"; break;
                    case "+": $this->pcre_regex .= " \\+"; break;
                    case "(": $this->pcre_regex .= "\\("; break;
                    case ")": $this->pcre_regex .= "\\)"; break;
                    case "%": $this->pcre_regex .= "%"; break;
                }
            } else if ($char === "!") {
                ;
            } else if (ord($char) >= 32) 
                $this->pcre_regex .= $char;
            else {
                $this->pcre_regex = "";
                return false;
            }
        }
        return true;
    }
}
?>
