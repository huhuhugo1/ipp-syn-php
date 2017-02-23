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
