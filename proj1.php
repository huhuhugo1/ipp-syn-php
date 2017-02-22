<?php header("Content-type: text/plain; charset=utf-8");

$br = false;    
$format_path = NULL;
$input_path = "php://stdin";
$output_path = "php://stdout";

function processArguments() {
    global $argc, $argv;
    global $br, $format_path, $input_path, $output_path; 

    $arguments = getopt("", array("help::","br::","format::","input::","output::"));

    //Unknown, damaged or recurrent switch
    if (($num_of_args = count($arguments)) != $argc - 1) {
        fwrite(STDERR, "Argument error!\n");
        exit(1);
    }

    //--help argument was entered
    if (array_key_exists("help", $arguments)) {
        if ($argc == 2 &&  $arguments["help"] == false) {
            print("This is help.\n");
            exit(0);
        } else {
            fwrite(STDERR, "Argument error!\n");
            exit(1);
        }
    }

    //<br> flag
    if (array_key_exists("br", $arguments)) {
        if ($arguments["br"] == false)
            $br = true;
        else {
            fwrite(STDERR, "Argument error!\n");
            exit(1);
        }
    }

    //Format file
    if (array_key_exists("format", $arguments))
        $format_path = $arguments["format"];
    
    //Input file
    if (array_key_exists("input", $arguments)) 
        $input_path = $arguments["input"];
    
    //Output file
    if (array_key_exists("output", $arguments))
        $output_path = $arguments["output"];
}

function regexConvert($ipp_regex) {
    $perl_regex = "";
    $negation = "";

    for ($i = 0; $i < mb_strlen($ipp_regex, "UTF-8"); $i++) {
        switch ($char = mb_substr($ipp_regex, $i, 1, "UTF-8")) {
            //Any other character (including '|', '*', '+', '(', ')')
            default: 
                if (ord($char) >= 32) {
                    $perl_regex .= $char;
                } else {
                    ;
                }
                break;
            
            //In IPP-regex diffrent meaning like in PERL-regex
            case ".":
                break;
            
            //In IPP-regex without special meaning, in PERL escape meaning
            case "\\": 
                $perl_regex .= "\\\\"; 
                break;
            
            //IN IPP-regex escape meaning, in PERL no
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
                    case "+": $perl_regex .= " \\+"; break;
                    case "(": $perl_regex .= "\\("; break;
                    case ")": $perl_regex .= "\\)"; break;
                    case "%": $perl_regex .= "%"; break;
                } break;
            
            //In IPP-regex operator, in PERL diffrent operator
            case "!": 
                break;
        }
    }
    return $perl_regex;
}

function parseFormatFile() {
    global $format_path;
    $format_list = array();

    if (is_file($format_path) == false)
        return $format_list;
    
    $format_file = fopen($format_path, "r");
    while ($row = fgets($format_file)) {
        $row = mb_ereg_replace ("\n$", "", $row);
        $row = mb_split("\t+", $row);
        $row[1] = mb_split(",[ \t]*", $row[1]);
        //
        for ($i = 0; $i < count($row[1]); $i++) {
            if ($row[1][$i] == "bold") {
                $row[1][$i] = "<b>\\1</b>";    
            } else if ($row[1][$i] == "italic") {
                $row[1][$i] = "<i>\\1</i>"; 
            } else if ($row[1][$i] == "underline") {
                $row[1][$i] = "<u>\\1</u>"; 
            } else if ($row[1][$i] == "teletype") {
                $row[1][$i] = "<tt>\\1</tt>"; 
            } else if (1/*size*/) {

            } else if(1/*color*/) {

            } else {
                fwrite(STDERR, "Invalid format of input file!\n");
                exit(4);
            }
        }
        array_push($format_list, $row);
    }
    
    fclose($format_file);
    var_dump($format_list);
    return $format_list;
}

function highlightFile($format_list) {
    global $br, $input_path, $output_path;
    
    if ($content = file_get_contents($input_path) == false) {
        fwrite(STDERR, "Invalid input file!\n");
        exit(2);
    }
    
    //...
    
    return $content;
}


processArguments();
print(highlightFile($input_path, parseFormatFile($format_path)));

?>
