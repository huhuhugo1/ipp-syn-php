<?php 

class document {
    private $document;
    private $table;

    function __construct() {
        $this->document = "";
        $this->table = array();
    }

    function __toString() {
        return $this->document;
    }

    function initFromFile($input_path) {
        return $this->document = file_get_contents($input_path);
    }

    function findRegexMatchPositions($regex) {
        $end = 0;
        while ($error = @preg_match("/(" . $regex->pcre_regex . ")/u", $this->document, $arr, PREG_OFFSET_CAPTURE, $end)) {
            $arr = array_reverse($arr[0]);
            $end = $arr[0]+strlen($arr[1]);
            $this->table[$regex->ipp_regex][] = array($arr[0], $end);   
        }
        return $error;
    }

    function highlightDocument($regex, $Tags) {
        if (array_key_exists ($regex, $this->table))
            foreach ($this->table[$regex] as &$coordinates){
                $this->insertSubstring($Tags[0], $coordinates[0]);
                $this->updateRegexMatchPositions($coordinates[0], strlen($Tags[0]));
                $this->insertSubstring($Tags[1], $coordinates[1]);
                $this->updateRegexMatchPositions($coordinates[1], strlen($Tags[1]));
            }
    }

    function updateRegexMatchPositions($idx, $len) {
        foreach ($this->table as &$regex)
            foreach($regex as &$cors)
                foreach($cors as &$cor)
                    if ($cor >= $idx)
                        $cor += $len;
    }

    function insertSubstring ($substring, $offset) {
        $this->document = mb_strcut($this->document, 0, $offset) . $substring . mb_strcut($this->document, $offset);
    }
}
?>
