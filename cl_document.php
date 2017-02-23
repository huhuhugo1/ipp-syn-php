<?php 

class document {
    private $document;
    private $table;

    function __construct() {
        $this->document = "";
        $this->table = array();
    }

    function initFromFile($format_path) {
        if ($this->document = file_get_contents($input_path) == false)
            return false;
        else
            return true;
    }

    function findRegexMatchPositions($regex) {
        mb_regex_encoding('UTF-8');
        mb_ereg_search_init($this->document);
        $output = array();
        while ($arr = mb_ereg_search_pos($regex)) {
            $output[] = array($arr[0], $arr[0]+$arr[1]);
        }
        $this->table[$regex] = $output;
    }

    function highlightDocument($regex, $opening, $closing) {
        foreach ($this->table[$regex] as $coordinates)
            foreach ($coordinates as $coordiante) {
                insertSubstring($opening, $coordiante[0]);
                updateRegexMatchPositions($coordiante[0], strlen($opening));
                insertSubstring($closing, $coordiante[1]);
                updateRegexMatchPositions($coordiante[1], strlen($closing));
            }
    }

    private function updateRegexMatchPositions($idx, $len) {
        foreach ($this->table as &$regex)
            foreach($regex as &$cors)
                foreach($cors as &$cor)
                    if ($cor >= $idx)
                        $cor += $len;
    }

    private function insertSubstring ($substring, $offset) {
        $this->document = mb_substr($this->document, 0, $offset) . $substring . mb_substr($this->document, $offset, NULL);
    }
}
?>
