<?php

class TimeSlot {
    public $day;// M/T/W/R
    public $startTime;// 24-hr time
    public $endTime;// 24-hr time
    public $location;// see excel file/documentation for letter codes
    public $studyGroupName = '';// will be left blank if no study group for this time slot

    public function set($input) {// takes data in format: day-start time-end time-location code.  Could benefit from more error-checking
        $data = explode('-', $input);
        if ($data[0] == 'M' || $data[0] == 'T' || $data[0] == 'W' || $data[0] == 'R') {// make sure day is valid
            $this->day = $data[0];
        }
        else {
            return false;// error
        }
        if ((int)explode(':', $data[1])[0] > 0 && (int)explode(':', $data[2])[0] <= 24) {// make sure start/end times are valid.
            $this->startTime = (int)explode(':', $data[1])[0];
            $this->endTime = (int)explode(':', $data[2])[0];
        }
        else {
            return false;// error
        }
        if ($data[3] == 'G' || $data[3] == 'L' || $data[3] == 'S' || $data[3] == 'B') {
            $this->location = $data[3];
        }
        else {
            return false;// error
        }
        if ($data[4] != '') {
            $this->studyGroupName = $data[4];
        }
        return true;// all data was valid and all variables have been initialized
    }
}

class Tutor {
    public $firstName = '';
    public $lastName = '';
    public $courses = array();
    public $times = array();
    public $location;//only used when specifc timeslots are being passed around.
    public $studyGroupName;//only used when specifc timeslots are being passed around.
}

function getAllTutors() {// This function gets called a lot.  It may be necessary to do something with static or otherwise limit the number of file reads.
    // tutor data
    $tutorData = array();
    foreach (explode(PHP_EOL, file_get_contents('data/tutors.csv')) as $line) {// break file into individual lines
        array_push($tutorData, explode(',', $line));
    }
    unset($tutorData[0]);// remove table headers

    // tutor times
    $timeData = array();
    foreach (explode(PHP_EOL, file_get_contents('data/times.csv')) as $line) {// break file into individual lines
        array_push($timeData, explode(',', $line));
    }
    unset($timeData[0]);// remove header row

    // parsing/validating
    $tutors = array();
    foreach ($tutorData as $line) {// this could be prettier.  Also could benefit from additional validation.
        $tutor = new Tutor;
        if (strlen($line[0]) > 0) {// First Name
            $tutor->firstName = str_replace(' ', '', $line[0]);
            if (strlen($line[1]) > 0) {// Last Name
                $tutor->lastName = str_replace(' ', '', $line[1]);
                foreach (array_slice($line, 2) as $course) {// individual courses
                    $course = rtrim($course);//remove any whitespace that may sneak into the file such as newlines or trailing spaces
                    array_push($tutor->courses, $course);
                }
                foreach ($timeData as $timeRow) {
                    if (str_replace(' ', '', $timeRow[0]) == $tutor->firstName && str_replace(' ', '', $timeRow[1]) == $tutor->lastName) {// check if name matches the current tutor being processed
                        foreach (array_slice($timeRow, 2) as $timeString) {// individual times
                            $timeSlot = new TimeSlot;
                            if ($timeSlot->set($timeString) != false) {// check if value was able to be parsed
                                array_push($tutor->times, $timeSlot);
                            }
                        }
                    }
                }
                array_push($tutors, $tutor);
            }
        }

    }
    return $tutors;
}

function getAllCourses() {
    $tutors = getAllTutors();
    $courses = array();
    foreach ($tutors as $tutor) {
        foreach ($tutor->courses as $course) {
            if(!in_array($course, $courses) && $course != NULL){
                array_push($courses, $course);
            }
        }
    }
    sort($courses);
    return $courses;
}

function selectTutorsByTime($day, $startTime, $endTime) {// day: M/T/W/R, start/end time: 24-hr
    $selection = array();
    $tutors = getAllTutors();
    foreach ($tutors as $tutor) {
        foreach ($tutor->times as $time) {
            if ($time->day == $day && $time->endTime > $startTime && $time->startTime < $endTime) {
                $tutor->location = $time->location;
                $tutor->studyGroupName = $time->studyGroupName;
                array_push($selection, $tutor);
            }
        }
    }
    return $selection;
}

?>
