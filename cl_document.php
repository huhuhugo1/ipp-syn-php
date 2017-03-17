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

    //Loading file content
    function initFromFile($input_path) {
        return $this->document = @file_get_contents($input_path);
    }

    //Finding regex matches
    function findRegexMatchPositions($regex) {
        $end = 0;
        while ($error = @preg_match("/(" . $regex->pcre_regex . ")/u", $this->document, $arr, PREG_OFFSET_CAPTURE, $end)) {
            $arr = array_reverse($arr[0]);
            if (strlen($arr[1]) === 0) {
                if ($end === strlen($this->document))
                    return 0;
                $end++;
                continue;
            }
            $end = $arr[0]+strlen($arr[1]);
            $this->table[$regex->ipp_regex][] = array($arr[0], $end);   
        }
        return $error;
    }

    //Inserting HTML tags
    function highlightDocument($regex, $Tags) {
        if (array_key_exists ($regex, $this->table))
            foreach ($this->table[$regex] as &$coordinates){
                $this->insertSubstring($Tags[0], $coordinates[0]);
                $this->updateRegexMatchPositions($coordinates[0], strlen($Tags[0]));
                $this->insertSubstring($Tags[1], $coordinates[1]);
                $this->updateRegexMatchPositions($coordinates[1], strlen($Tags[1]));
            }
    }

    //Updating positions of matches after inserting tag
    function updateRegexMatchPositions($idx, $len) {
        foreach ($this->table as &$regex)
            foreach($regex as &$cors) {
                if ($cors[0] >= $idx)
                    $cors[0] += $len;
                
                if ($cors[1] > $idx)
                    $cors[1] += $len;
            }
    }

    //Insert string to specific byte
    function insertSubstring ($substring, $offset) {
        $this->document = mb_strcut($this->document, 0, $offset) . $substring . mb_strcut($this->document, $offset);
    }

    //If true, enables <br />
    function enableBr($br) {
        if ($br)
            $this->document = mb_ereg_replace("\n", "<br />\n", $this->document);
    }
}
?>
