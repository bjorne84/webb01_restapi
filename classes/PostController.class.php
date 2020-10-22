<?php

use FFI\CData;

class PostController extends CheckInputController
{
    public function getDataFromTables($table, $id)
    {
        /* Sanitize input from*/
        $table = filter_var($table, FILTER_SANITIZE_STRING);
        $id = filter_var($id, FILTER_SANITIZE_STRING);
        /* Make lowercase*/
        $table = strtolower($table);
        $id = strtolower($id);

        // Starts different call-method to the database depending on input
        if ($id > 0 && $table === 'courses') {
            $result = $this->getCoursesByType($table, $id);
            return $result;
            exit();
        } else if ($table === 'courses') {
            $result = $this->getAllCourses();
            return $result;
            exit();
        } else if (($table != 'courses') && ($table != "")) {
            /* Start method that get all the data for a specific table*/
            $result = $this->getAllData($table);
            return $result;
            exit();
        }
    }

    /******* HTTP_METHOD POST **********/

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



                /* Check if it´s a new post or update (true or false)
        if($cData['New']) {
            //$kursnamn = $cData['Indata']['Languages_id'][0];
            $kursnamn = $cData['Indata']['Languages_id'];
            foreach($kursnamn as $kurs) {
                
            }
            http_response_code(200); // Fel på server
            $result = ["message" => $kursnamn];
            return $result;
            exit();
        } else {
            http_response_code(503); // Fel på server
            $result = ["message" => "En uppdatering."];
            return $result;
            exit();
        }*/
        }
    }

    protected function courses($cData, $case, $check) {
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



    // method for
    protected function portfolio($cData, $case, $check) {
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