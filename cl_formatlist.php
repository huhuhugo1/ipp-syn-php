<?php 
class FormatList {
    public $format_list = array();

    function initFromFile($format_path) {
        
        if (is_file($format_path) == false)
            return $format_list;
        
        $format_file = fopen($format_path, "r");
        while ($row = fgets($format_file)) {
            $row = mb_ereg_replace ("\n$", "", $row);
            $row = mb_split("\t+", $row, 2);
            $row[1] = mb_split(",[ \t]*", $row[1]);
            array_push($format_list, $row);
        }
        
        fclose($format_file);
        return $format_list;
    }
}
?>
