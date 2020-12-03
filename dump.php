<?php
/*
protected function courses($cData, $case, $check) {
        // initiate class for the input controll of data to match what the database accept
        $check = new CheckInputController();
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
                $result = ["message" => "Success, Course added."];
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