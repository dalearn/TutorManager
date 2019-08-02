<?php
include 'data.php';
include 'config.php';

$config = new TutorManagerConfig();

function locationNameToCode() {// special helper function for the location dropdown.
    $location = htmlspecialchars($_GET['location']);
    if ($location == 'Library') {
        return 'L';
    }
    else if ($location == 'Student Center') {
        return 'S';
    }
    else if ($location == 'Beckley') {
        return 'B';
    }
}

function generateTableCell($day, $timeSlot) {// generate the actual text to go in each table cell
    $selection = selectTutorsByTime($day, $timeSlot, $timeSlot + 1);

    $L = false;// used to tell if that location has already been entered.
    $S = false;
    $B = false;

    $text = '';
    foreach ($selection as $tutor) {
        if (in_array(htmlspecialchars($_GET['course']), $tutor->courses) || htmlspecialchars($_GET['course']) == 'all' || htmlspecialchars($_GET['course']) == NULL) {// return based on value of course selection menu

            if ($tutor->location == locationNameToCode() || htmlspecialchars($_GET['location']) == 'all' || htmlspecialchars($_GET['location']) == NULL) {// check if location matches selections in location selection menu
                if (htmlspecialchars($_GET['tutor']) == ($tutor->firstName . ' ' . mb_substr($tutor->lastName, 0, 1, 'utf-8')) || htmlspecialchars($_GET['tutor']) == 'all' || htmlspecialchars($_GET['tutor']) == NULL) {// search by tutor name

                    if ($tutor->location == 'L' && !$L){//these are pretty ugly and could be combined with the locationNameToCode() function somehow.
                        if (!(!$L && !$S && !$B)) {// don't put newline before first location, otherwise put newline
                            $text .= '<br>';
                        }
                        $text .= '<b><a onclick="showLocationModal(\'L\')">Library:</a></b><br>';
                        $L = true;
                    }
                    else if ($tutor->location == 'S' && !$S){
                        if (!(!$L && !$S && !$B)) {// don't put newline before first location, otherwise put newline
                            $text .= '<br>';
                        }
                        $text .= '<b><a onclick="showLocationModal(\'S\')">Student Center Lounge:</a></b><br>';
                        $S = true;
                    }
                    else if ($tutor->location == 'B' && !$B){
                        if (!(!$L && !$S && !$B)) {// don't put newline before first location, otherwise put newline
                            $text .= '<br>';
                        }
                        $text .= '<b><a onclick="showLocationModal(\'B\')">Beckley Campus:</a></b><br>';
                        $B = true;
                    }
                    else {
                        mb_substr($text, 0, 3, 'utf-8');
                    }
                    $text .= '<b><a onclick="showNameModal(\'' . $tutor->firstName . '\',\'' . mb_substr($tutor->lastName, 0, 1, 'utf-8') . '\')">' . $tutor->firstName . ' ' . mb_substr($tutor->lastName, 0, 1, 'utf-8') . ':</a></b> ';
                    if ($tutor->studyGroupName != NULL) {
                        $text .= $tutor->studyGroupName;
                    }
                    else {
                        foreach ($tutor->courses as $course) {
                            if ($course != NULL) {
                                $text .= ' ' . $course . ',';
                            }
                        }
                    }
                    $text = rtrim($text, ',');// remove trailing commas.
                    $text .= '<br>';
                }
            }
        }
    }
    $text = rtrim($text, '<br>');//remove trailing <br>
    return $text;
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.js"></script>
        <script>
            function showLocationModal(locationCode) {
                if (locationCode == 'L') {
                    $('.ui.modal.library')
                        .modal('show')
                    ;
                }
                else if (locationCode == 'S') {
                    $('.ui.modal.studentCenter')
                        .modal('show')
                    ;
                }
                else if (locationCode == 'B') {
                    $('.ui.modal.beckley')
                        .modal('show')
                    ;
                }
            }
            function showNameModal(firstName, lastInitial) {
                $('.ui.modal.' + firstName + lastInitial)
                    .modal('show')
                ;
            }
        </script>
        <style>
            a {
                color: black;
            }
            a:hover {
                color: black;
                text-decoration: underline;
            }
            u {
                text-decoration: underline;
            }
        </style>
        <title>TutorManager</title>
    </head>
    <body>
        <div class="ui container" style="margin-top: 1.5em;">
            <h1 class="ui header"><?php echo $config->semesterName ?> Drop-In Tutoring Schedule</h1>
            <div class="ui segment">
                <div class="ui segment">
                    <form action="" method="get">
                        <select class="ui selection dropdown" name="course">
                            <option value="all">All Courses</option>
                            <?php
                                foreach (getAllCourses() as $course) {
                                    echo '<option ';
                                    if (htmlspecialchars($_GET['course']) == $course) {
                                        echo 'selected';
                                    }
                                    echo " value=\"$course\">$course</option>";
                                }
                            ?>
                        </select>
                        <select class="ui selection dropdown" name="location">
                            <option value="all">All Locations</option>
                            <option <?php if (htmlspecialchars($_GET['location']) == 'Library') {echo 'selected';} ?> value="Library">Library</option>
                            <option <?php if (htmlspecialchars($_GET['location']) == 'Student Center') {echo 'selected';} ?> value="Student Center">Student Center Study Lounge</option>
                            <option <?php if (htmlspecialchars($_GET['location']) == 'Beckley') {echo 'selected';} ?> value="Beckley">Beckley Campus</option>
                        </select>
                        <select class="ui selection dropdown" name="tutor">
                            <option value="all">All Tutors</option>
                            <?php
                                foreach (getAllTutors() as $tutor) {
                                    echo '<option ';
                                    if (htmlspecialchars($_GET['tutor']) == $tutor->firstName . ' ' . mb_substr($tutor->lastName, 0, 1, 'utf-8')) {
                                        echo 'selected';
                                    }
                                    echo ' value="' . $tutor->firstName . ' ' . mb_substr($tutor->lastName, 0, 1, 'utf-8') . '">' . $tutor->firstName . ' ' . mb_substr($tutor->lastName, 0, 1, 'utf-8') . '</option>';
                                }
                            ?>
                        </select>
                        <button class="ui right floated primary button" type="submit">Filter</button>
                    </form>
                </div>
                <table class="ui unstackable celled striped definition table">
                    <thead>
                        <tr>
                            <th class="one wide"></th>
                            <th class="three wide center aligned">Monday</th>
                            <th class="three wide center aligned">Tuesday</th>
                            <th class="three wide center aligned">Wednesday</th>
                            <th class="three wide center aligned">Thursday</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            for ($i = 10; $i <= 22; $i++) {// i = hour (24-hr time)
                                echo "<tr>
                                    <td class=\"center aligned\">" . ltrim(date('h:i A', strtotime($i . ':00')), '0') . "</td>
                                    <td>" . generateTableCell('M', $i) . "</td>
                                    <td>" . generateTableCell('T', $i) . "</td>
                                    <td>" . generateTableCell('W', $i) . "</td>
                                    <td>" . generateTableCell('R', $i) . "</td>
                                </tr>";
                            }
                        ?>
                    </tbody>
                </table>
                <p>
                    <?php
                        echo $config->pageText;
                     ?>
                </p>
            </div>
        </div>

        <!-- modals -->
        <!-- need a way to reduce duplication between these modals.  Something like Angular would be ideal but unfortunately this project wasn't build with it from the ground up and it would be wasteful to use it just for this.  PHP function is another option but also could get ugly with HTML involved.  Need to come back to this later. -->
        <div class="ui longer modal library">
            <i class="close icon"></i>
            <div class="header">
                Library
            </div>
            <div class="image content">
                <div class="ui medium square image">
                    <img src="/data/locations/library/image.jpg">
                </div>
                <div class="description">
                    <p><?php echo file_get_contents('data/locations/library/description.txt');?></p>
                </div>
            </div>
        </div>
        <div class="ui longer modal studentCenter">
            <i class="close icon"></i>
            <div class="header">
                Student Center
            </div>
            <div class="image content">
                <div class="ui big rounded image">
                    <img src="/data/locations/studentCenter/image.jpg">
                </div>
                <div class="description">
                    <p><?php echo file_get_contents('data/locations/studentCenter/description.txt');?></p>
                </div>
            </div>
        </div>
        <div class="ui longer modal beckley">
            <i class="close icon"></i>
            <div class="header">
                Beckley
            </div>
            <div class="image content">
                <div class="ui medium square image">
                    <img src="/data/locations/beckley/image.jpg">
                </div>
                <div class="description">
                    <p><?php echo file_get_contents('data/locations/beckley/description.txt');?></p>
                </div>
            </div>
        </div>
        <!-- tutor description modals -->
        <!-- these are even worse than the previous ones since they don't load data from the server and involve a lot of duplication.  In reality, Angular would be best here but the angular file would still be bigger than all of these put together and would require a complete re-write of much of the project.-->
        <?php
            foreach (getAllTutors() as $tutor) {
                $courses = '';
                foreach ($tutor->courses as $course) {// generate list of courses
                    if ($course != NULL) {
                        $courses .= ' ' . $course . ',';
                    }
                }
                $courses = rtrim($courses, ',');// remove trailing commas.

                $times = '';
                foreach ($tutor->times as $timeSlot) {// generate list of timeslots
                    if ($timeSlot->day == 'M') {
                        $times .= 'Monday ';
                    }
                    else if ($timeSlot->day == 'T') {
                        $times .= 'Tuesday ';
                    }
                    else if ($timeSlot->day == 'W') {
                        $times .= 'Wednesday ';
                    }
                    else if ($timeSlot->day == 'R') {
                        $times .= 'Thursday ';
                    }
                    $times .= ltrim(date('h:i A', strtotime($timeSlot->startTime . ':00')), '0') . ' - ' . ltrim(date('h:i A', strtotime($timeSlot->endTime . ':00')), '0') . '<br>';//make times look pretty
                }

                echo '
                <div class="ui longer modal ' . $tutor->firstName . mb_substr($tutor->lastName, 0, 1, 'utf-8') . '">
                    <i class="close icon"></i>
                    <div class="header">
                        ' . $tutor->firstName . ' ' . mb_substr($tutor->lastName, 0, 1, 'utf-8') . '.
                    </div>
                    <div class="image content">
                        <div class="ui medium square image">
                            <img src="/data/tutors/' . $tutor->firstName . mb_substr($tutor->lastName, 0, 1, 'utf-8') . '.jpg">
                        </div>
                        <div class="description">
                            <div class="ui header">Courses</div>
                            <p>' . $courses . '</p>
                            <div class="ui header">Tutoring Times</div>
                            <p>' . $times . '</p>
                        </div>
                    </div>
                </div>';
            }
         ?>
    </body>
</html>
