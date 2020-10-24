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

    // Gets the languages for a specific course 
    protected function getLanguages($id)
    {
        $sql = "SELECT Language
        FROM Bridge_language bi
        JOIN Courses c ON c.Course_ID = bi.Course_ID
        JOIN Language l ON l.Language_ID = bi.Language_ID
            WHERE c.Course_ID = $id";
        $result = $this->connect()->query($sql);
        return $result->fetchAll();
        exit();
    }

    // Gets all courses including education/scholl
    protected function getCoursesAndEducation()
    {
        $sql = "SELECT courses.*,education.Programme, education.School
	FROM courses 
	JOIN education 
		ON courses.Education_ID = education.Education_ID
        ORDER BY Education_ID DESC, Course_ID";
        $result = $this->connect()->query($sql);
        return $result->fetchAll();
        exit();
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

    protected function getAllDataById($table, $id_type, $id)
    {
        // SQL frågagit
        $sql = "SELECT * FROM $table WHERE $id_type = $id";
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
                $lastElement = end($last_id);

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

    // method to check if it´s just the course-data that should update or languages also
    protected function setUpdateCourse($cData)
    {
        // If there is no languages data, just run the updateJustCourse method
        if (!isset($cData['Indata']['Languages_id'])) {
            // Start updateJustCourse, if it works, return true else false
            if ($this->updateJustCourse($cData)) {
                return true;
            } else {
                return false;
            }
        } // If languages data is set 
        else if (isset($cData['Indata']['Languages_id'])) {

            $Course_ID = $cData['Id_push'];
            // Get all bridge-data from the course
            $bridge = $this->getBridgeById($Course_ID);
            //var_dump($bridge);
            //Check if bridge-data exist and if so delete all data
            if (!empty($bridge)) {
                $this->deleteBridgeLanguage($Course_ID);
            }

            // Insert new bridge-data
            if ($this->updateJustCourse($cData)) {
                /* Grab languagesid and then loop all langueas assosiated with the course */
                $languages = $cData['Indata']['Languages_id'];
                foreach ($languages as $lang_id) {
                    $this->setLanguagesTable($Course_ID, $lang_id);
                }
                return true;
            } else {
                // If setJustCourse did not work return false
                return false;
            }
        }
    }


    // method to update the course data and not languages
    protected function updateJustCourse($cData)
    {
        //var_dump($cData);
        // Create variables
        $Course_ID = $cData['Id_push'];
        $Education_ID = $cData['Indata']['Education_ID'];
        $CourseName = $cData['Indata']['CourseName'];
        $Points = $cData['Indata']['Points'];
        $Grade = $cData['Indata']['Grade'];

        // SQL QUERY with preperade statement for security
        $sql = "UPDATE courses
        SET Education_ID = ?, CourseName = ?, Points = ?, Grade = ?
        WHERE Course_ID = $Course_ID";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([$Education_ID, $CourseName, $Points, $Grade]);
        return true;
    }

    // Check if bridge-data exist
    protected function getBridgeById($id)
    {
        $sql = "SELECT * FROM bridge_language WHERE Course_ID = $id";
        $result = $this->connect()->query($sql);
        return $result->fetchAll();
    }

    // Delete all bridge_language data for chosen course
    protected function deleteBridgeLanguage($id)
    {
        $sql = "DELETE FROM bridge_language WHERE Course_ID = $id";
        $this->connect()->query($sql);
        return true;
    }

    // ****** DELETE *****************
    // ID type is the pk in the table, ex Course_ID
    protected function deleteById($table, $idType, $id)
    {
        $sql = "DELETE FROM $table WHERE $idType = $id";
        $this->connect()->query($sql);
        return true;
    }

    /* **************************************************
    ************* portfolio ***************************
    *****************************************************/

    // DELETE
    //get bridge_prtfolio_languages, the then is used to check if data
    protected function getBridgePortById($id)
    {
        $sql = "SELECT * FROM bridge_portfolio_language WHERE Portfolio_ID = $id";
        $result = $this->connect()->query($sql);
        return $result->fetchAll();
    }

    // Delete all bridge_portfolio_language data for chosen course
    protected function deleteBridgePortLanguage($id)
    {
        $sql = "DELETE FROM bridge_portfolio_language WHERE Portfolio_ID = $id";
        $this->connect()->query($sql);
        return true;
    }

    // NEW - portfolio data
    protected function setPortfolio($cData)
    {
        // If there is no languages data, just run the setJustCourse method
        if (!isset($cData['Indata']['Bridge_portfolio_id'])) {
            // Start setJustCourse id that works, return true else false
            if ($this->setJustPortfolio($cData)) {
                return true;
            } else {
                return false;
            }
        } // If languages data is set 
        else if (isset($cData['Indata']['Bridge_portfolio_id'])) {
            if ($this->setJustPortfolio($cData)) {
                /* If the setJustPortfolio worked then start the method
                to first get last set id for portfolio. Then use that id in the
                setLanguaugestable method which set the bridgetable in database. So 
                we need the course id to be set first */
                // Variables to use
                $table = 'portfolio';
                $pkIdName = 'Portfolio_ID';
                $last_id = $this->getLast_ID($table, $pkIdName);
                //var_dump($last_id);
                //Set the internal pointer to the end.
                $lastElement = end($last_id);

                $id_portfolio = $lastElement['Portfolio_ID'];

                $languages = $cData['Indata']['Bridge_portfolio_id'];
                foreach ($languages as $lang_id) {
                    $this->setBridgePortLang($id_portfolio, $lang_id);
                }
                return true;
            } else {
                // If setJustPortfolio did not work return false
                return false;
            }
        }
    }

    // Set the just the portfoliotable-data
    protected function setJustPortfolio($cData)
    {
        // Create variables
        $Titel = $cData['Indata']['Titel'];
        $URL = $cData['Indata']['URL'];
        $Image_url = $cData['Indata']['Image_url'];
        $Description = $cData['Indata']['Description'];
        // SQL QUERY with preperade statement for security
        $sql = "INSERT INTO portfolio (Titel, URL, Image_url, Description) VALUES(?, ?, ?, ?)";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([$Titel, $URL, $Image_url, $Description]);
        return true;
    }

    // Get last all ID
    protected function getLast_ID($table, $pkIdName)
    {

        $sql = "SELECT $pkIdName FROM $table";
        $result = $this->connect()->query($sql);
        return $result->fetchall();

        // return $last_id;
    }

    // Set the bridge_langauge
    protected function setBridgePortLang($lastId, $id_language)
    {
        $sql = "INSERT INTO bridge_portfolio_language (Portfolio_ID, Language_ID) VALUES($lastId, ?)";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([$id_language]);
        return true;
    }

    // Update - portfolio data
    // method to check if it´s just the course-data that should update or languages also
    protected function setUpdatePortfolio($cData)
    {
        // If there is no languages data, just run the updateJustCourse method
        if (!isset($cData['Indata']['Bridge_portfolio_id'])) {
            // Start updateJustCourse, if it works, return true else false
            if ($this->updateJustCourse($cData)) {

                return true;
            } else {

                return false;
            }
        } // If languages data is set 
        else if (isset($cData['Indata']['Bridge_portfolio_id'])) {

            $Portfolio_ID = $cData['Id_push'];
            // Get all bridge-data from the course
            $bridge = $this->getBridgePortById($Portfolio_ID);
            //var_dump($bridge);
            //Check if bridge-data exist and if so delete all data
            if (!empty($bridge)) {
                $this->deleteBridgePortLanguage($Portfolio_ID);
            }

            // Insert new bridge-data
            if ($this->updateJustPortfolio($cData)) {
                /* Grab languagesid and then loop all langueas assosiated with the course */
                $languages = $cData['Indata']['Bridge_portfolio_id'];
                foreach ($languages as $lang_id) {
                    $this->setLanguagesTable($Portfolio_ID, $lang_id);
                }
                return true;
            } else {
                // If setJustCourse did not work return false
                return false;
            }
        }
    }

    // update the just the portfoliotable-data
    protected function updateJustPortfolio($cData)
    {
        // Create variables
        $Portfolio_ID = $cData['Id_push'];
        $Titel = $cData['Indata']['Titel'];
        $URL = $cData['Indata']['URL'];
        $Image_url = $cData['Indata']['Image_url'];
        $Description = $cData['Indata']['Description'];
        // SQL QUERY with preperade statement for security
        $sql = "UPDATE portfolio
           SET Titel = ?, URL = ?, Image_url = ?, Description = ?
           WHERE Portfolio_ID = $Portfolio_ID";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([$Titel, $URL, $Image_url, $Description]);
        return true;
    }


    /* **************************************************
    ************* work_experience ***************************
    *****************************************************/

    /* HTTP-METHOD DELETE
    Using the deleteById() method called from PostController.class
    */

    // HTTP-METHOD POST 
    //Set the work_experience table
    protected function setWork($cData)
    {
        // Create variables
        $Workplace = $cData['Indata']['Workplace'];
        $Titel = $cData['Indata']['Titel'];
        $Description = $cData['Indata']['Description'];
        $Startdate = $cData['Indata']['Startdate'];
        $Enddate = $cData['Indata']['Enddate'];
        // SQL QUERY with preperade statement for security
        $sql = "INSERT INTO work_experience (Workplace, Titel, Description, Startdate, Enddate) VALUES(?, ?, ?, ?, ?)";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([$Workplace, $Titel, $Description, $Startdate, $Enddate]);
        return true;
    }

    // HTTP-METHOD PUT = update 
    // update the work_experience table
    protected function updateWork($cData)
    {
        // Create variables
        $CV_ID = $cData['Id_push'];
        $Workplace = $cData['Indata']['Workplace'];
        $Titel = $cData['Indata']['Titel'];
        $Description = $cData['Indata']['Description'];
        $Startdate = $cData['Indata']['Startdate'];
        $Enddate = $cData['Indata']['Enddate'];
        // SQL QUERY with preperade statement for security
        $sql = "UPDATE work_experience
               SET Workplace = ?, Titel = ?, Description = ?, Startdate = ?, Enddate = ?
               WHERE CV_ID = $CV_ID";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([$Workplace, $Titel, $Description, $Startdate, $Enddate]);
        return true;
    }

    /* **************************************************
    ************* language ***************************
    *****************************************************/

    /* HTTP-METHOD DELETE
    Using the deleteById() method called from PostController.class
    */

    // HTTP-METHOD POST 
    //Set the work_experience table
    protected function setLanguage($cData)
    {
        // Create variables
        $Language = $cData['Indata']['Language'];
        $Img_url = $cData['Indata']['Img_url'];
        // SQL QUERY with preperade statement for security
        $sql = "INSERT INTO language (Language, Img_url) VALUES(?, ?)";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([$Language, $Img_url]);
        return true;
    }

    // HTTP-METHOD PUT = update 
    // update the work_experience table
    protected function updateLanguage($cData)
    {
        // Create variables
        $Language_ID = $cData['Id_push'];
        $Language = $cData['Indata']['Language'];
        $Img_url = $cData['Indata']['Img_url'];
        // SQL QUERY with preperade statement for security
        $sql = "UPDATE language
               SET Language = ?, Img_url = ?
               WHERE Language_ID = $Language_ID";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([$Language, $Img_url]);
        return true;
    }
}
