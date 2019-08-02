<?php
class TutorManagerConfig {
    public $semesterName = '';
    public $pageText = '';

    function __construct() {
        $configData = array();
        foreach (explode(PHP_EOL, file_get_contents('data/config.csv')) as $line) {// break file into individual lines
            $line = explode(',', $line);
            $list = array();
            foreach ($line as $cell) {
                if ($cell != '') {
                    array_push($list, $cell);
                }
            }
            array_push($configData, $list);
        }
        unset($configData[0]);// remove table headers

        foreach ($configData as $entry) {// take simplified list and set config variables
            if ($entry[0] == 'Semester Name:') {
                $this->semesterName = $entry[1];
            }
        }

        $this->pageText = file_get_contents('data/pagetext.txt');
    }
}
