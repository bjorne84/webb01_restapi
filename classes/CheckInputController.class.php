<?php
// Classsss that check input data so that it´s what the database exepts
class CheckInputController extends PostModel
{

    // Check the course table
    protected function controlCourse($cData)
    {
        /* If either of required fields are empty, error array with input-data and message. */
        if (empty($cData['Indata']['Education_ID']) || empty($cData['Indata']['CourseName'])) {
            http_response_code(400); //400 Bad Request
            $errorMsg = ["Message" => "Education_ID and CourseName needs to set"];
            return $errorMsg;
            exit();
        }
        // ----- Contoll ID is numeric
        if (!is_numeric($cData['Indata']['Education_ID'])) {
            http_response_code(400); //400 Bad Request
            $errorMsg = ["Message" => "ID måste skickas med och består endast av siffor!"];
            return $errorMsg;
            exit();
        }

        // ----- Controll lenght of input data --------
        // Course_Name max 100 characters
        if (mb_strlen($cData['Indata']['CourseName']) >= 99) {
            http_response_code(400); //400 Bad Request
            $errorMsg = ["Message" => "CourseName can be max 100 characters"];
            return $errorMsg;
            exit();
        }

        // Points max 15 characters
        if (mb_strlen($cData['Indata']['Points']) >= 15) {
            http_response_code(400); //400 Bad Request
            $errorMsg = ["Message" => "Points can be max 100 characters"];
            return $errorMsg;
            exit();
        }

        // Grade max 15 characters
        if (mb_strlen($cData['Indata']['Grade']) >= 5) {
            http_response_code(400); //400 Bad Request
            $errorMsg = ["Message" => "Points can be max 5 characters"];
            return $errorMsg;
            exit();
        }
        
        return $cData;
    }
}
