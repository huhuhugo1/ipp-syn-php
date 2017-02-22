<?php header("Content-type: text/plain; charset=utf-8");

$br = false;    
$format_path = NULL;
$input_path = "php://stdin";
$output_path = "php://stdout";

class Table {
    public $table;
    
    function addRegexMatchPositions($string, $regex) {
        mb_regex_encoding('UTF-8');
        mb_ereg_search_init($string);
        $output = array();
        while ($arr = mb_ereg_search_pos($regex)) {
            $output[] = array($arr[0], $arr[0]+$arr[1]);
        }
        $this->table[$regex] = $output;
    }

    function update($idx, $len) {
        foreach ($this->table as &$regex)
            foreach($regex as &$cors)
                foreach($cors as &$cor)
                    if ($cor >= $idx)
                        $cor += $len;
    }

    function getRegexMatchPositions($regex) {
        return $this->table[$regex];
    }
}

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
            }
        } else if ($char === "!") {
            ;
        } else if (ord($char) >= 32) 
            $perl_regex .= $char;
        else {
            fwrite(STDERR, "Invalid regex format!\n");
            exit(4);
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
        array_push($format_list, $row);
    }
    
    fclose($format_file);
    return $format_list;
}

function insertSubstring ($string, $substring, $offset) {
    return mb_substr($string, 0, $offset) . $substring. mb_substr($string, $offset, NULL);
}

function highlightFile() {
    global $br, $input_path, $output_path;
    
    $content = file_get_contents($input_path);
    $format_list = parseFormatFile();

    if ($content == false) {
        fwrite(STDERR, "Invalid input file!\n");
        exit(2);
    }

    $table = new Table;

    //find all regex in file -- 1st level
    foreach ($format_list as $regex_row) 
        $table->addRegexMatchPositions($content, $regex_row[0]);
    
    //apply regex -- 2nd level
    foreach ($format_list as $regex_row) {
        $opening = "";
        $closing = ""; 
        foreach ($regex_row[1] as $format_cmd) {
            if ($format_cmd == "bold") {
                $opening .= "<b>";
                $closing = "</b>" . $closing; 
            } else if ($format_cmd == "italic") {
                $opening .= "<i>";
                $closing = "</i>" . $closing;  
            } else if ($format_cmd == "underline") {
                $opening .= "<u>";
                $closing = "</u>" . $closing; 
            } else if ($format_cmd == "teletype") {
                $opening .= "<tt>";
                $closing = "</tt>" . $closing;  
            } else if (mb_ereg_match ("color:[0-9a-fA-F][0-9a-fA-F][0-9a-fA-F][0-9a-fA-F][0-9a-fA-F][0-9a-fA-F]" , $format_cmd)) {
                ;
            } else if (mb_ereg_match ("size:[0-7]" , $format_cmd)) {
                ;
            } else {
                fwrite(STDERR, "Invalid format of input file!\n");
                exit(4);
            }
        }

        $cords = $table->getRegexMatchPositions($regex_row[0]);
        for ($i = 0; $i < count($cords); $i++) {
            $cords = $table->getRegexMatchPositions($regex_row[0]);
            $content = insertSubstring($content, $opening, $cords[$i][0]);
            $table->update($cords[$i][0], strlen($opening));
            
            
            $cords = $table->getRegexMatchPositions($regex_row[0]);
            
            
            $content = insertSubstring($content, $closing, $cords[$i][1]);
            $table->update($cords[$i][1], strlen($closing));
        }
    }

    return $content;
}


processArguments();
print(highlightFile(parseFormatFile()));



//var_dump(getRegexMatchPosition("ahoj, ako sa máš?", "a"));

?>