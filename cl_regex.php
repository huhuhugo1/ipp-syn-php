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
        $common_metachars = array(".", "|", "*", "+", "(", ")");

        $regex_array = array();
        $element = $negation = "";

        for ($i = 0; $i < mb_strlen($this->ipp_regex, "UTF-8"); $i++) {
            $char = mb_substr($this->ipp_regex, $i, 1, "UTF-8");
            
            if (in_array($char, $PCRE_metachars))
                $element .= "[".$negation."\\".$char."]";

            else if (in_array($char, $common_metachars)) {
                if ($negation === "^") return false;
                $element .= $char;
            }
            
            else if ($char === "!") {
                if ($negation === "^") 
                    return false;
                $negation = "^"; continue;
            }
            
            else if ($char === "%")
                switch ($char = mb_substr($this->ipp_regex, ++$i, 1, "UTF-8")) {
                    case "s": $element .= "[".$negation." \\t\\n\\r\\f\\v]"; break;
                    case "a": $element .= "[".$negation."\\s\\S]"; break;
                    case "d": $element .= "[".$negation."[:digit:]]"; break;
                    case "l": $element .= "[".$negation."[:lower:]]"; break;
                    case "L": $element .= "[".$negation."[:upper:]]"; break;
                    case "w": $element .= "[".$negation."[:alpha:]]"; break;
                    case "W": $element .= "[".$negation."[:alnum:]]"; break;
                    case "t": $element .= "[".$negation."\\t]"; break;
                    case "n": $element .= "[".$negation."\\n]"; break;
                    case ".": $element .= "[".$negation."\\.]"; break;
                    case "|": $element .= "[".$negation."\\|]"; break;
                    case "!": $element .= "[".$negation."\\!]"; break;
                    case "*": $element .= "[".$negation."\\*]"; break;
                    case "+": $element .= "[".$negation."\\+]"; break;
                    case "(": $element .= "[".$negation."\\(]"; break;
                    case ")": $element .= "[".$negation."\\)]"; break;
                    case "%": $element .= "[".$negation."\\%]"; break;
                    default: return false; // %<nespecialni_symbol> je neplatny regularni vyraz
                }
            
            else if (ord($char) >= 32) 
                $element .= "[".$negation.$char."]";

            else
                return false;

            $regex_array[] = $element;
            $element = $negation = "";
        }

        if ($negation === "^")
            return false;

        for ($i = 0; $i < count($regex_array); $i++) {
            switch ($regex_array[$i]) {
                case ".": 
                    $regex_array[$i] = "";
                case "|":
                    if ($i === 0 || $i === count($regex_array)-1)
                        return false;  
                    if ($regex_array[$i-1] == "" || $regex_array[$i-1] == "|")
                        return false;
                    break;
            }

        }

        $this->pcre_regex = implode($regex_array);
        if(preg_match("/".$this->pcre_regex."/u", null) === false)
            return false;
        
        return true;
    }
}
?>
