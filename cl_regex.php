<?php 
class Regex {
    public $ipp_regex;
    public $pcre_regex;

    function __construct($ipp_regex) {
        $this->ipp_regex = $ipp_regex;
        $this->pcre_regex = "";
    }

    //Convert IPP regex to PCRE regex
    function convert() {          
        $PCRE_metachars = array("^", "$", "?", "[", "]", "{", "}", "\\", "-", "/"); // "/" - slash needs to be escaped
        $common_metachars = array(".", "|", "*", "+", "(", ")");

        $is_error[""] = array("" => true, "*" => true, "+" => true, "." => true, "|" => true, "(" => false, ")" => true);
        $is_error["*"] = array("" => false, "*" => false, "+" => false, "." => false, "|" => false, "(" => false, ")" => false);
        $is_error["+"] = array("" => false, "*" => false, "+" => false, "." => false, "|" => false, "(" => false, ")" => false);
        $is_error["."] = array("" => true, "*" => true, "+" => true, "." => true, "|" => true, "(" => false, ")" => true);
        $is_error["|"] = array("" => true, "*" => true, "+" => true, "." => true, "|" => true, "(" => false, ")" => true);
        $is_error["("] = array("" => true, "*" => true, "+" => true, "." => true, "|" => true, "(" => false, ")" => true);
        $is_error[")"] = array("" => false, "*" => false, "+" => false, "." => false, "|" => false, "(" => false, ")" => false);

        $regex_array = array();
        $element = $negation = "";

        for ($i = 0; $i < mb_strlen($this->ipp_regex, "UTF-8"); $i++) {
            $char = mb_substr($this->ipp_regex, $i, 1, "UTF-8");
            
            if (in_array($char, $PCRE_metachars))
                $element = "[".$negation."\\".$char."]";

            else if (in_array($char, $common_metachars)) {
                if ($negation === "^")
                    return false;
                $element = $char;
            }
            
            else if ($char === "!") {
                if ($negation === "^") 
                    return false;
                $negation = "^";
                continue;
            }
            
            else if ($char === "%")
                switch ($char = mb_substr($this->ipp_regex, ++$i, 1, "UTF-8")) {
                    case "s": $element = "[".$negation." \\t\\n\\r\\f\\v]"; break;
                    case "a": $element = "[".$negation."\\s\\S]"; break;
                    case "d": $element = "[".$negation."0-9]"; break;
                    case "l": $element = "[".$negation."a-z]"; break;
                    case "L": $element = "[".$negation."A-Z]"; break;
                    case "w": $element = "[".$negation."a-zA-Z]"; break;
                    case "W": $element = "[".$negation."a-zA-Z0-9]"; break;
                    case "t": $element = "[".$negation."\\t]"; break;
                    case "n": $element = "[".$negation."\\n]"; break;
                    case ".": $element = "[".$negation."\\.]"; break;
                    case "|": $element = "[".$negation."\\|]"; break;
                    case "!": $element = "[".$negation."\\!]"; break;
                    case "*": $element = "[".$negation."\\*]"; break;
                    case "+": $element = "[".$negation."\\+]"; break;
                    case "(": $element = "[".$negation."\\(]"; break;
                    case ")": $element = "[".$negation."\\)]"; break;
                    case "%": $element = "[".$negation."\\%]"; break;
                    default: return false; // %<nespecialni_symbol> je neplatny regularni vyraz
                }
            
            else if (ord($char) >= 32) 
                $element = "[".$negation.$char."]";

            else
                return false;

            $regex_array[] = $element;
            $element = $negation = "";
        }

        if ($negation === "^")
            return false;
        
        array_unshift($regex_array, "");
        $regex_array[] = "";

        for ($i = 0; $i < count($regex_array)-1; $i++) {
            if (@$is_error[$regex_array[$i]][$regex_array[$i+1]] === true)
                return false;

            if ($regex_array[$i] === ".") {
                $regex_array[$i] = "";
            } else if ($regex_array[$i] === "+") {
                if ($regex_array[$i+1] === "+" || $regex_array[$i+1] === "*")
                    $regex_array[$i] = "";
            } else if ($regex_array[$i] === "*") {
                if ($regex_array[$i+1] === "+" || $regex_array[$i+1] === "*") {
                    $regex_array[$i] = "";
                    $regex_array[$i+1] = "*";
                }
            }
        }

        $this->pcre_regex = implode($regex_array);
        if(@preg_match("/".$this->pcre_regex."/u", null) === false)
            return false;
        
        return true;
    }
}

?>
