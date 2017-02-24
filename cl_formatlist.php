<?php 
class FormatList {
    private $format_list;

    public function __construct() {
         $this->format_list = array();
    }

    public function gets() {
        return array_keys($this->format_list);
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
                $this->format_list[$row[0]] = mb_split(",[ \t]*", $row[1]); //TODO aktualne sa obsah prepise poslednym riadom s danym regexom, treba overit zadanie
            } else {
                fclose($format_file);
                return false;
            }
        }
        fclose($format_file);

        return $this->format_list;
    }

    public function getTags($ipp_regex) {
        $opening = "";
        $closing = "";
        $font = "";

        foreach ($this->format_list[$ipp_regex] as $format_cmd) {
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
            } else if (mb_ereg_match ("color:[0-9a-fA-F]{6}" , $format_cmd)) {
                $font .= "color=#" . mb_substr($format_cmd, 6);
            } else if (mb_ereg_match ("size:[0-7]" , $format_cmd)) {
                $font .= "size=" . mb_substr($format_cmd, 5);
            } else {//TODO mozmo zbytocne, viz riadok 21
                fwrite(STDERR, "POZRI TODO Invalid format of input file!\n");
                exit(4);
            }

            if ($font != "") {
                $opening .= "<font " . $font . ">";
                $closing = "</font>" . $closing;
            }
        }
        return array($opening, $closing);
    }
}
?>
