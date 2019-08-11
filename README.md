# TutorManager User Manual
## Overview
This document is an attempt to provide a user-friendly reference for usage of the TutorManager software.  All .csv files should be saved and edited in a comma-delimited format using a spreadsheet program such as Microsoft Excel.

## File Specifications
### `data/config.csv`
While this file was originally intended to be used for a number of configurable parameters, most parameters were replaced by adding `pagetext.txt` which is an entirely user configurable document displayed under the main schedule.  This file currently contains one setting titled "Semester Name" which will be rendered at the top of the page.

### `data/tutors.csv`
This document is a reference listing of all tutors and the courses they are eligible to tutor.  It is critical that data in this document be accurate because it is the first step when accessing every other .csv file.  The first two cells in each row should contain the first and last name of the tutor followed by course identifiers in each following cell.  Course identifiers are  interpreted as strings of indefinite-length and may be written any way desired.  "ENGL 101", "English 101", and "Composition and Rhetoric I" would all be valid course titles.

### `data/times.csv`
This document contains the times each tutor specified in `data/tutors.csv` is scheduled to work.  The first two cells of each row should contain the first and last name of the tutor.  Each subsequent cell should contain a time entry in the dash-separated format: `day (M/T/W/R)-start time (24-hr)-end time(24 hr)-location code (L=Library, S=Student Center Lounge, B=Beckley)-Study Group Name (optional)`.  A valid example would be:  `R-13:00-16:00-S-CHEM 101 Study Group` which specifies that the tutor will be available from  13:00PM to 16:00PM on Thursdays in the Student Center Study Lounge with the CHEM 101 Study Group.  

### `data/pagetext.txt`
The contents of this file are rendered at the bottom of the page under the schedule view.  Directions, contact numbers, and general information can be put here.  HTML is allowed.

### `data/tutors/*`
For every tutor added to `data/tutors.csv`, a corresponding image named `FirstNameLastInitial.jpg` should be added to this folder.  While the photo can be displayed at any size, it is recommended that photos be cropped to be square for consistency.  

### `data/locations/*`
For each location directory listed in `data/locations/`, each should contain a file named `decription.txt` that contains text about that location and a corresponding image named `image.jpg`  It is recommended but not required for images to be square.
