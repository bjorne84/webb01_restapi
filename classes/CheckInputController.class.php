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
            $errorMsg = ["message" => "Education_ID and CourseName needs to set"];
            return $errorMsg;
            exit();
        }
        // ----- Contoll ID is numeric
        if (!is_numeric($cData['Indata']['Education_ID'])) {
            http_response_code(400); //400 Bad Request
            $errorMsg = ["message" => "ID måste skickas med och består endast av siffor!"];
            return $errorMsg;
            exit();
        }

        // ----- Controll lenght of input data --------
        // Course_Name max 100 characters
        if (mb_strlen($cData['Indata']['CourseName']) >= 99) {
            http_response_code(400); //400 Bad Request
            $errorMsg = ["message" => "CourseName can be max 100 characters"];
            return $errorMsg;
            exit();
        }

        // Points max 15 characters
        if (mb_strlen($cData['Indata']['Points']) >= 14) {
            http_response_code(400); //400 Bad Request
            $errorMsg = ["message" => "Points can be max 15 characters"];
            return $errorMsg;
            exit();
        }

        // Grade max 15 characters
        if (mb_strlen($cData['Indata']['Grade']) >= 6) {
            http_response_code(400); //400 Bad Request
            $errorMsg = ["message" => "Grade can be max 5 characters"];
            return $errorMsg;
            exit();
        }

        return $cData;
    }

    // Check the course table
    protected function controlportfolio($cData)
    {
        /* If either of required fields are empty, returns error array with input-data and message. */
        if (empty($cData['Indata']['Titel']) || empty($cData['Indata']['URL']) || empty($cData['Indata']['Description'])) {
            http_response_code(400); //400 Bad Request
            $errorMsg = ["message" => "Titel, URL and Description needs to set"];
            return $errorMsg;
            exit();
        }

        // ----- Controll lenght of input data --------
        // Course_Name max 100 characters
        if (mb_strlen($cData['Indata']['Titel']) >= 200) {
            http_response_code(400); //400 Bad Request
            $errorMsg = ["message" => "Titel can be max 200 characters"];
            return $errorMsg;
            exit();
        }

        // Points max 15 characters
        if (mb_strlen($cData['Indata']['URL']) >= 150) {
            http_response_code(400); //400 Bad Request
            $errorMsg = ["message" => "URL can be max 150 characters"];
            return $errorMsg;
            exit();
        }

        return $cData;
    }
}
