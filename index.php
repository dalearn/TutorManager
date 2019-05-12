<?php
include 'data.php';

function generateTableCell($day, $timeSlot) {//generate the actual text to go in each table cell
    $selection = selectTutorsByTime($day, $timeSlot, $timeSlot + 1);

    $text = '';
    foreach ($selection as $tutor) {
        $text .= $tutor->firstName . ' ' . $tutor->lastName . ': ';
        foreach ($tutor->courses as $course) {
            if ($course != NULL) {
                $text .= ' ' . $course . ',';
            }
        }
        $text = substr($text, 0, -1);//remove last comma.  for some reason rtrim() does not work here
        $text .= '<br>';
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
                    <select class="ui dropdown">
                        <option value="">All Courses</option>
                        <option value=""></option>
                    </select>
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
