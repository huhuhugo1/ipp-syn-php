<?php header("Content-type: text/plain; charset=utf-8");

include('cl_document.php');
include('cl_formatlist.php');
include('cl_regex.php');

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

processArguments();

$format_list = new FormatList;
if ($format_list->initFromFile($format_path) === false) {
    fwrite(STDERR, "Invalid format of formating file!\n");
    exit(4);
}

$document = new Document;
if ($document->initFromFile($input_path) === false) {
    fwrite(STDERR, "Invalid input file!\n");
    exit(2);
}

foreach ($format_list->gets() as $ipp_regex) {
    $regex = new Regex($ipp_regex);
    if ($regex->convert() === false || $document->findRegexMatchPositions($regex) === false) {
        fwrite(STDERR, "Invalid regex!\n");
        exit(4);
    }
}

foreach ($format_list->gets() as $ipp_regex) {
    $document->highlightDocument($ipp_regex, $format_list->getTags($ipp_regex));
}

$document->enableBr($br);

if (file_put_contents($output_path, $document) === false){
    fwrite(STDERR, "Output file error!\n");
    exit(3);
}

?>
