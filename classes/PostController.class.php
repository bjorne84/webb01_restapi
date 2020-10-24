<?php

use FFI\CData;

class PostController extends CheckInputController
{
    // GET HTTP-METHOD
    public function getDataFromTables($table, $id)
    {
        /* Sanitize input from*/
        $table = filter_var($table, FILTER_SANITIZE_STRING);
        $id = filter_var($id, FILTER_SANITIZE_STRING);
        /* Make lowercase*/
        $table = strtolower($table);
        $id = strtolower($id);

        // Check if table is set, otherwise send out errormessage
        if ($table === "") {
            http_response_code(400); // Bad request
            $result = ["message" => "In the address bar you need to set tablename, like: https://webb01.se/restapi/?table=courses"];
            return $result;
            exit();
        }

        // Starts different call-method to the database depending on input
        if ($id > 0 && $table === 'courses') {
            $result = $this->getCoursesByType($table, $id);
            return $result;
            exit();
        } else if ($table === 'courses') {
            $result = $this->getAllCourses();
            return $result;
            exit();
        } else if (($table != 'courses') && ($table != "") && empty($id)) {
            /* Start method that get all the data for a specific table*/
            $result = $this->getAllData($table);
            return $result;
            exit();
        } else if (($table != 'courses') && ($id != "")) {
            // Shows data from a table by id
            $id_type = $this->getId_type($table);
            $result = $this->getAllDataById($table, $id_type, $id);
            return $result;
            exit();
        }
    }

    // method to get id_type
    protected function getId_type($table)
    {
        switch ($table) {
            case 'portfolio':
                return 'Portfolio_ID';
                break;
            case 'language':
                return 'Language_ID';
                break;
            case 'work_experience':
                return 'CV_ID';
                break;
            case 'education':
                return 'Education_ID';
                break;
            case 'bridge_language':
                return 'Course_ID';
                break;
            case 'bridge_portfolio_language':
                return 'Portfolio_ID`';
                break;
        }
    }

    /******* HTTP_METHOD POST, PUT AND DELETE **********/

    /* Controll wich table the input to send data to*/
    public function whichTableInput($case)
    {
        // initiate class for the input controll of data to match what the database accept
        $check = new CheckInputController();
        /* file_get_contents, hämtar rå data innan den hamnar i superglobaler som post och get*/
        $inputJSON = file_get_contents('php://input');
        // Från json till php
        $input = json_decode($inputJSON, TRUE); //convert JSON into array
        // Sanerera data och lägg array i ny variabel.
        $cData = filter_var_array($input, FILTER_SANITIZE_SPECIAL_CHARS);
        // var_dump($cData);

        // check if table is set. (maybe put in a own method)
        if (!isset($cData['Table'])) {
            http_response_code(400); // Bad request
            $result = ["message" => "Json-filen måste ha Table satt, annars vet vi ej vilken tabell som skall göras."];
            return $result;
            exit();
        }
        // Variable for table
        $table = $cData['Table'];

        if ($table === 'courses') {
        }
        switch ($table) {
            case 'courses':
                $result = $this->courses($cData, $case, $check);
                return $result;
                exit();
                //
                break;
            case 'portfolio':
                $result = $this->portfolio($cData, $case, $check);
                return $result;
                exit();
                break;
            case 'work_experience':
                $result = $this->workExperience($cData, $case, $check);
                return $result;
                exit();
                break;
            case 'language':
                $result = $this->language($cData, $case, $check);
                return $result;
                exit();
                break;
        }
    }

    protected function courses($cData, $case, $check)
    {
        // initiate class for the input controll of data to match what the database accept
        // Check if delete is set
        if ($case === "delete") {
            // Create variables
            $Course_ID = $cData['Id_push'];
            $idType = $cData['Id_type'];
            $tableName = $cData['Table'];
            // Check if language-data exist and if so, then delete it
            $bridge = $this->getBridgeById($Course_ID);
            if (!empty($bridge)) {
                $this->deleteBridgeLanguage($Course_ID);
            }
            // Call method to delete the course
            if ($this->deleteById($tableName, $idType, $Course_ID)) {
                http_response_code(200); // Succes OK 
                $result = ["message" => "Success, Course deleted."];
                return $result;
                exit();
            } else {
                http_response_code(500); // Internal Server Error
                $result = ["message" => "Error, could not delete course from database."];
                return $result;
                exit();
            }
        } else if ($case === "new") {
            // If its new course to be added
            // Start method to controll the data
            $cData = $check->controlCourse($cData);
            //Check if message exist, which means that errormessage was send from controlCourse
            if (array_key_exists('message', $cData)) {
                $result = $cData['message'];
                return $result;
                exit();
            }
            // Method to set new course
            if ($this->setCourse($cData)) {
                http_response_code(201); // Created
                $result = ["message" => "Succes, Course added."];
                return $result;
                exit();
            } else {
                http_response_code(500); // Internal Server Error
                $result = ["message" => "Error, could not add course in database."];
                return $result;
                exit();
            }
        } else if ($case === "update") {
            // Start method to controll the data
            $cData = $check->controlCourse($cData);
            //Check if message exist, which means that errormessage was send from controlCourse
            if (array_key_exists('message', $cData)) {
                $result = $cData['message'];
                return $result;
                exit();
            }
            //Method to update
            if ($this->setUpdateCourse($cData)) {
                http_response_code(201); // Created
                $result = ["message" => "Success, Course updated."];
                return $result;
                exit();
            } else {
                http_response_code(500); // Internal Server Error
                $result = ["message" => "Error, could not add course in database."];
                return $result;
                exit();
            }
        }
    }



    // method for table: portfolio
    protected function portfolio($cData, $case, $check)
    {
        // initiate class for the input controll of data to match what the database accept
        // Check if delete is set
        if ($case === "delete") {
            // Create variables
            $Portfolio_ID = $cData['Id_push'];
            $idType = $cData['Id_type'];
            $tableName = $cData['Table'];
            // Check if language-data exist and if so, then delete it
            $bridge = $this->getBridgePortById($Portfolio_ID);
            if (!empty($bridge)) {
                $this->deleteBridgeLanguage($Portfolio_ID);
            }
            // Call method to delete the project
            if ($this->deleteById($tableName, $idType, $Portfolio_ID)) {
                http_response_code(200); // Succes OK 
                $result = ["message" => "Success, project deleted in portfolio."];
                return $result;
                exit();
            } else {
                http_response_code(500); // Internal Server Error
                $result = ["message" => "Error, could not delete project in portfolio table from database."];
                return $result;
                exit();
            }
        } else if ($case === "new") {
            // If its new course to be added
            // Start method to controll the data
            $cData = $check->controlportfolio($cData);
            //Check if message exist, which means that errormessage was send from controlCourse
            if (array_key_exists('message', $cData)) {
                $result = $cData['message'];
                return $result;
                exit();
            }
            // Method to set new course
            if ($this->setPortfolio($cData)) {
                http_response_code(201); // Created
                $result = ["message" => "Succes, portfolio project added."];
                return $result;
                exit();
            } else {
                http_response_code(500); // Internal Server Error
                $result = ["message" => "Error, could not add course in database."];
                return $result;
                exit();
            }
        } else if ($case === "update") {
            // Start method to controll the data
            $cData = $check->controlportfolio($cData);
            //Check if message exist, which means that errormessage was send from controlCourse
            if (array_key_exists('message', $cData)) {
                $result = $cData['message'];
                return $result;
                exit();
            }
            //Method to update
            if ($this->setUpdatePortfolio($cData)) {
                http_response_code(201); // Created
                $result = ["message" => "Success, portfolio project updated."];
                return $result;
                exit();
            } else {
                http_response_code(500); // Internal Server Error
                $result = ["message" => "Error, could not update portfolio project in database."];
                return $result;
                exit();
            }
        }
    }

    // method for table: work_experience
    protected function workExperience($cData, $case, $check)
    {
        // initiate class for the input controll of data to match what the database accept
        // Check if delete is set
        if ($case === "delete") {
            // Create variables
            $CV_ID = $cData['Id_push'];
            $tableName = $cData['Table'];
            $id_type = $this->getId_type($tableName);

            // Call method to delete row
            if ($this->deleteById($tableName, $id_type, $CV_ID)) {
                http_response_code(200); // Succes OK 
                $result = ["message" => "Success, work deleted in work_experience."];
                return $result;
                exit();
            } else {
                http_response_code(500); // Internal Server Error
                $result = ["message" => "Error, could not delete work in work_experience table in the database."];
                return $result;
                exit();
            }
        } else if ($case === "new") {
            // If its new course to be added
            // Start method to controll the data
            //$cData = $check->controlportfolio($cData);
            //Check if message exist, which means that errormessage was send from controlCourse
            if (array_key_exists('message', $cData)) {
                $result = $cData['message'];
                return $result;
                exit();
            }
            // Method to set new course
            if ($this->setWork($cData)) {
                http_response_code(201); // Created
                $result = ["message" => "Success, work added."];
                return $result;
                exit();
            } else {
                http_response_code(500); // Internal Server Error
                $result = ["message" => "Error, could not add work in database."];
                return $result;
                exit();
            }
        } else if ($case === "update") {
            // Start method to controll the data
            // $cData = $check->controlportfolio($cData);
            //Check if message exist, which means that errormessage was send from controlCourse
            if (array_key_exists('message', $cData)) {
                $result = $cData['message'];
                return $result;
                exit();
            }
            //Method to update
            if ($this->updateWork($cData)) {
                http_response_code(201); // Created
                $result = ["message" => "Success, work_experience table updated."];
                return $result;
                exit();
            } else {
                http_response_code(500); // Internal Server Error
                $result = ["message" => "Error, could not update work_experience table in database."];
                return $result;
                exit();
            }
        }
    }

    // method for table: language
    protected function language($cData, $case, $check)
    {
        // initiate class for the input controll of data to match what the database accept
        // Check if delete is set
        if ($case === "delete") {
            // Create variables
            $Language_ID = $cData['Id_push'];
            $tableName = $cData['Table'];
            $id_type = $this->getId_type($tableName);

            // Call method to delete row
            if ($this->deleteById($tableName, $id_type, $Language_ID)) {
                http_response_code(200); // Succes OK 
                $result = ["message" => "Success, work deleted in language table."];
                return $result;
                exit();
            } else {
                http_response_code(500); // Internal Server Error
                $result = ["message" => "Error, could not delete row in language table in the database."];
                return $result;
                exit();
            }
        } else if ($case === "new") {
            // If its new course to be added
            // Start method to controll the data
            //$cData = $check->controlportfolio($cData);
            //Check if message exist, which means that errormessage was send from controlCourse
            if (array_key_exists('message', $cData)) {
                $result = $cData['message'];
                return $result;
                exit();
            }
            // Method to set new course
            if ($this->setLanguage($cData)) {
                http_response_code(201); // Created
                $result = ["message" => "Success, language data added."];
                return $result;
                exit();
            } else {
                http_response_code(500); // Internal Server Error
                $result = ["message" => "Error, could not add language-data in database."];
                return $result;
                exit();
            }
        } else if ($case === "update") {
            // Start method to controll the data
            // $cData = $check->controlportfolio($cData);
            //Check if message exist, which means that errormessage was send from controlCourse
            if (array_key_exists('message', $cData)) {
                $result = $cData['message'];
                return $result;
                exit();
            }
            //Method to update
            if ($this->updateLanguage($cData)) {
                http_response_code(201); // Created
                $result = ["message" => "Success, language data updated."];
                return $result;
                exit();
            } else {
                http_response_code(500); // Internal Server Error
                $result = ["message" => "Error, could not update language data."];
                return $result;
                exit();
            }
        }
    }
}















/*
    } else if (($table != 'courses') || ($table != "")) {
            $result = $this->getAllData($table);
            if(sizeof($result) > 0) {
                return $result;
                exit();
            } else {
                http_response_code(404); // Not found
                $result = ["message" => "Databasen hittar inget på angivna parametrar"];
                return $result;
                exit();
            }
            
            else {
            http_response_code(404); // Not found
            $result = ["message" => "Ingenting hittat med angivna prameterar i address-raden. Läs ReadMe-filen!"];
            return $result;
            exit();
        }
            */