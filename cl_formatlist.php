<?php 
class FormatList {
    private $format_list;

    public function __construct() {
         $this->format_list = array();
    }

    public function get() {
         return $this->format_list;
    }

    public function initFromFile($format_path) {
        if (is_file($format_path) === false || ($format_file = fopen($format_path, "r")) === false) {
            return $this->format_list;
        }

        while ($row = fgets($format_file)) {
            if ($row === "\n")
                continue;
            if (mb_ereg_match("^[^\t]+\t+(bold|italic|underline|teletype|color:[0-9a-fA-F]{6}|size:[0-7])(,[ \t]*(bold|italic|underline|teletype|color:[0-9a-fA-F]{6}|size:[0-7]))*\n?$", $row)) {
                $row = mb_ereg_replace ("\n$", "", $row);
                $row = mb_split("\t+", $row, 2);
                $row[1] = mb_split(",[ \t]*", $row[1]);
                array_push($this->format_list, $row);
            } else {
                fclose($format_file);
                return false;
            }
        }
        fclose($format_file);

    return $this->format_list;
    }
}
?>
