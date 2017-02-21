<?php header("Content-type: text/plain; charset=utf-8");

$ipp_regex = "(100|%d%d|%d)%%";

$br = false;    
$format = NULL;
$input = STDIN;
$output = STDOUT;

function processArguments() {
    global $argc, $argv;
    global $br, $format, $input, $output; 

    $arguments = getopt("", array("help::","br::","format::","input::","output::"));
    
    //var_dump($arguments);

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
        if ($arguments["br"] == false) {
            $br = true;
        } else {
            fwrite(STDERR, "Argument error!\n");
            exit(1);
        }
    }

    //Format file
    if (array_key_exists("format", $arguments)) {
        if (is_file($arguments["format"])) {
            $format = fopen($arguments["format"], "r");
        }
    }

    //Input file
    if (array_key_exists("input", $arguments)) {
        if (is_file($arguments["input"])) {
            $input = fopen($arguments["input"], "r");
        } else {
            fwrite(STDERR, "Invalid input file!\n");
            exit(2);
        }
    }

    //Output file
    if (array_key_exists("output", $arguments)) {
        if ($arguments["output"] == false || $output = fopen($arguments["output"], "w") == false) {
            fwrite(STDERR, "Invalid output file!\n");
            exit(3);
        }
    }
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

function parseFormatFile($format_file) {
    $format_list = array();
    while ($row = fgets($format_file)) {
        $row = mb_substr ($row, 0, -1);
        $row = mb_split("\t+", $row);
        $row[1] = mb_split(",[ \t]*", $row[1]);
        array_push($format_list, $row);
    }
    return $format_list;
}
processArguments();
var_dump(parseFormatFile($format));
//print(regexConvert($ipp_regex));
?>
