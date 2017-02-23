<?php
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
?>
