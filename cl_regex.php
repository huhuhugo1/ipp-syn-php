<?php 
class Regex {
    public $ipp_regex;
    public $pcre_regex;

    function __construct($ipp_regex) {
        $this->ipp_regex = $ipp_regex;
        $this->pcre_regex = "";
    }

    function convert() {          
        $PCRE_metachars = array("^", "$", "?", "[", "]", "{", "}", "\\", "-", "/"); // "/" - slash needs to be escaped
        $common_metachars = array("|", "*", "+", "(", ")");

        $negation = "";
        $dot = false;

        for ($i = 0; $i < mb_strlen($this->ipp_regex, "UTF-8"); $i++) {
            $char = mb_substr($this->ipp_regex, $i, 1, "UTF-8");
            
            if (in_array($char, $PCRE_metachars))
                $this->pcre_regex .= "[".$negation."\\".$char."]";

            else if (in_array($char, $common_metachars)) {
                if ($negation === "^")
                    return false;
                $this->pcre_regex .= $char;
            }
            
            else if ($char === ".") {
                if ($dot || $i === 0 || $i === mb_strlen($this->ipp_regex, "UTF-8") - 1)
                    return false;
                $dot = true;
                continue;
            }
            
            else if ($char === "!") {
                $negation = "^";
                continue;
            }
            
            else if ($char === "%")
                switch ($char = mb_substr($this->ipp_regex, ++$i, 1, "UTF-8")) {
                    case "s": $this->pcre_regex .= "[".$negation." \\t\\n\\r\\f\\v]"; break;
                    case "a": $this->pcre_regex .= "[".$negation."\\s\\S]"; break;
                    case "d": $this->pcre_regex .= "[".$negation."0-9]"; break;
                    case "l": $this->pcre_regex .= "[".$negation."a-z]"; break;
                    case "L": $this->pcre_regex .= "[".$negation."A-Z]"; break;
                    case "w": $this->pcre_regex .= "[".$negation."a-zA-Z]"; break;
                    case "W": $this->pcre_regex .= "[".$negation."0-9a-zA-Z]"; break;
                    case "t": $this->pcre_regex .= "[".$negation."\\t]"; break;
                    case "n": $this->pcre_regex .= "[".$negation."\\n]"; break;
                    case ".": $this->pcre_regex .= "[".$negation."\\.]"; break;
                    case "|": $this->pcre_regex .= "[".$negation."\\|]"; break;
                    case "!": $this->pcre_regex .= "[".$negation."\\!]"; break;
                    case "*": $this->pcre_regex .= "[".$negation."\\*]"; break;
                    case "+": $this->pcre_regex .= "[".$negation."\\+]"; break;
                    case "(": $this->pcre_regex .= "[".$negation."\\(]"; break;
                    case ")": $this->pcre_regex .= "[".$negation."\\)]"; break;
                    case "%": $this->pcre_regex .= "[".$negation."\\%]"; break;
                    default: return false; // %<nespecialni_symbol> je neplatny regularni vyraz
                }
            
            else if (ord($char) >= 32) 
                $this->pcre_regex .= "[".$negation.$char."]";

            else {
                $this->pcre_regex = "";
                return false;
            }

            $negation = "";
            $dot = false;
        }

        if(preg_match("/".$this->pcre_regex."/u", null) === false)
            return false;
        return true;
    }
}
?>
