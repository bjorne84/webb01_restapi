<?php
class PostModel extends Dbc
{
    // Get all courses
    protected function getAllCourses()
    { {
            $sql = "SELECT * FROM `courses` ORDER by `Education_ID`";
            $result = $this->connect()->query($sql);
            return $result->fetchAll();
            exit();
        }
    }
    // Gets all data from the table user chooses 
    protected function getAllData($table)
    {
        $sql = "SELECT * FROM $table";
        $result = $this->connect()->query($sql);
        return $result->fetchAll();
        exit();
    }

    // When you want to get all the courses from the education/school
    protected function getCoursesByType($table, $id)
    {
        $sql = "SELECT * FROM $table WHERE Education_ID = $id";
        $result = $this->connect()->query($sql);
        return $result->fetchAll();
        exit();
    }

    protected function getAll()
    {
        // SQL fråga
        $sql = "SELECT * FROM courses";
        $result = $this->connect()->query($sql);
        return $result->fetchAll();
        exit();
    }


    // SET NEW DATA IN DATANBASE
    protected function setCourse($cData)
    {
        // If there is no languages data, just run the setJustCourse method
        if (!isset($cData['Indata']['Languages_id'])) {
            // Start seJustCourse id that works, return true else false
            if ($this->setJustCourse($cData)) {
                return true;
            } else {
                return false;
            }
        } // If languages data is set 
        else if (isset($cData['Indata']['Languages_id'])) {
            if ($this->setJustCourse($cData)) {
                /* If the setJustCourse worked then start the method
                to first get last set id for courses. Then use that id in the
                setLanguaugestable method which set the bridgetable in database. So 
                we need the course id to be set first */
                $last_id = $this->getLastCourse_ID();
                //var_dump($last_id);
                //Set the internal pointer to the end.
                $lastElement = end( $last_id );

                $id_course = $lastElement['Course_ID'];

                $languages = $cData['Indata']['Languages_id'];
                foreach ($languages as $lang_id) {
                    $this->setLanguagesTable($id_course, $lang_id);
                }
                return true;
            } else {
                // If setJustCourse did not work return false
                return false;
            }
        }
    }

    // Set the Course
    protected function setJustCourse($cData)
    {
        // Create variables
        $Education_ID = $cData['Indata']['Education_ID'];
        $CourseName = $cData['Indata']['CourseName'];
        $Points = $cData['Indata']['Points'];
        $Grade = $cData['Indata']['Grade'];
        // SQL QUERY with preperade statement for security
        $sql = "INSERT INTO courses (Education_ID, CourseName, Points, Grade) VALUES(?, ?, ?, ?)";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([$Education_ID, $CourseName, $Points, $Grade]);
        return true;
    }

    // Get last Course_ID
    protected function getLastCourse_ID()
    {
        $sql = "SELECT `Course_ID` FROM `courses`";
        $result = $this->connect()->query($sql);
        return $result->fetchall();

        // return $last_id;
    }

    // Set the bridge_langauge
    protected function setLanguagesTable($lastCourse_id, $id_language)
    {
        $sql = "INSERT INTO bridge_language (Course_ID, Language_ID) VALUES($lastCourse_id, ?)";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([$id_language]);
        return true;
    }


    // **** Update data *****

    protected function updateJustCourse($cData)
    {
        // Create variables
        $Education_ID = $cData['Indata']['Education_ID'];
        $CourseName = $cData['Indata']['CourseName'];
        $Points = $cData['Indata']['Points'];
        $Grade = $cData['Indata']['Grade'];
        // SQL QUERY with preperade statement for security
        $sql = "INSERT INTO courses (Education_ID, CourseName, Points, Grade) VALUES(?, ?, ?, ?)";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([$Education_ID, $CourseName, $Points, $Grade]);
        return true;
    }
    //

}
