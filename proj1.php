<?php header("Content-type: text/plain; charset=utf-8");

$ipp_regex = "(100|%d%d|%d)%%";

function regexConvert($ipp_regex) {
   $perl_regex = "";
   $negation = "";

   for ($i = 0; $i < mb_strlen($ipp_regex, "UTF-8"); $i++) {
      switch ($char = mb_substr($ipp_regex, $i, 1, "UTF-8")) {
         default: $perl_regex .= $char; break;
         case "\\": $perl_regex .= "\\\\"; break;
         case "!": break;
         case "%":
            $i++;
            switch ($char = mb_substr($ipp_regex, $i, 1, "UTF-8")) {
               case "s": $perl_regex .= "[\\t\\n\\r\\f\\v]"; break;
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
               case "+": $perl_regex .= "\\+"; break; 
               case "(": $perl_regex .= "\\("; break; 
               case ")": $perl_regex .= "\\)"; break; 
               case "%": $perl_regex .= "%"; break; 
            } break; 
      }
   }
   return $perl_regex;
}

print(regexConvert($ipp_regex));
?>
