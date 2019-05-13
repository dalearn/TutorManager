<?php
include 'data.php';

function locationNameToCode() {// special helper function for the location dropdown.  This could certainly become an enum or similar.
    $location = htmlspecialchars($_GET['location']);
    if ($location == 'GRIP') {
        return 'G';
    }
    else if ($location == 'Library') {
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

    $text = '';
    foreach ($selection as $tutor) {
        if (in_array(htmlspecialchars($_GET['course']), $tutor->courses) || htmlspecialchars($_GET['course']) == 'all' || htmlspecialchars($_GET['course']) == NULL) {// return based on value of course selection menu

            if ($tutor->location == locationNameToCode() || htmlspecialchars($_GET['location']) == 'all' || htmlspecialchars($_GET['location']) == NULL) {// check if location matches selections in location selection menu
                if (htmlspecialchars($_GET['tutor']) == ($tutor->firstName . ' ' . $tutor->lastName) || htmlspecialchars($_GET['tutor']) == 'all' || htmlspecialchars($_GET['tutor']) == NULL) {
                    if ($tutor->location == 'G'){//these are pretty ugly and could be combined with the locationNameToCode() function somehow.
                        $text .= '<b>GRIP Center:</b><br>';
                    }
                    else if ($tutor->location == 'L'){
                        $text .= '<b>Library:</b><br>';
                    }
                    else if ($tutor->location == 'S'){
                        $text .= '<b>Student Center Lounge:</b><br>';
                    }
                    else if ($tutor->location == 'B'){
                        $text .= '<b>Beckley Campus:</b><br>';
                    }
                    $text .= $tutor->firstName . ' ' . $tutor->lastName . ': ';
                    foreach ($tutor->courses as $course) {
                        if ($course != NULL) {
                            $text .= ' ' . $course . ',';
                        }
                    }
                    $text = rtrim($text, ',');// remove last comma.
                    $text .= '<br><br>';
                }
            }
        }
    }
    return $text;
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.js"></script>
        <title>TutorManager</title>
    </head>
    <body>
        <div class="ui container" style="margin-top: 1.5em;">
            <div class="ui segment">
                <div class="ui segment">
                    <form action="" method="get">
                        <select class="ui selection dropdown" name="course">
                            <option value="all">All Courses</option>
                            <?php
                                foreach (getAllCourses() as $course) {// this is pretty ugly.  Should it get its own function or would that be too much for something this small?
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
                            <option <?php if (htmlspecialchars($_GET['location']) == 'GRIP') {echo 'selected';} ?> value="GRIP">GRIP Center</option>
                            <option <?php if (htmlspecialchars($_GET['location']) == 'Library') {echo 'selected';} ?> value="Library">Library</option>
                            <option <?php if (htmlspecialchars($_GET['location']) == 'Student Center') {echo 'selected';} ?> value="Student Center">Student Center Study Lounge</option>
                            <option <?php if (htmlspecialchars($_GET['location']) == 'Beckley') {echo 'selected';} ?> value="Beckley">Beckley Campus</option>
                        </select>
                        <select class="ui selection dropdown" name="tutor">
                            <option value="all">All Tutors</option>
                            <?php
                                foreach (getAllTutors() as $tutor) {
                                    echo '<option ';
                                    if (htmlspecialchars($_GET['tutor']) == $tutor->firstName . ' ' . $tutor->lastName) {
                                        echo 'selected';
                                    }
                                    echo ' value="' . $tutor->firstName . ' ' . $tutor->lastName . '">' . $tutor->firstName . ' ' . $tutor->lastName . '</option>';
                                }
                            ?>
                        </select>
                        <button class="ui right floated primary button" type="submit">Filter</button>
                    </form>
                </div>
                <table class="ui celled striped definition table">
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
            </div>
        <div>
    </body>
</html>
