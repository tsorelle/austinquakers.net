<?php
  // Let's load the required scripts for this file
  require_once(__DIR__ . DIRECTORY_SEPARATOR . '../vr_api_client.php');

  /**
   * DISCLAIMER! (Please read this first)
   * The following PHP script demonstrates some of the capabilities of this wrapper.
   * The goal of this wrapper is to help you get started with the VR API. The wrapper provides
   * insights into connecting and making VR API calls. You can extend this and create your own
   * custom application. The wrapper does not cover all the API calls VR provides. For a full
   * list of API calls VR provides, please refer to the documentation. This software is
   * provided "as-is", please note that VerticalResponse will not maintain or update this.
   */

  /**
   * This file provides examples of the different error types that can appear while using this wrapper
   * In this file we will show you
   * > HOW TO: Handle possible exceptions that might occur while using this wrapper
   *   Example #1: Handle a request made with invalid parameters
   *   Example #2: Handle a request made with invalid credentials
   */

  // First a little configuration:
  // Set your VR API credentials
  putenv('VR_API_CLIENT_ID=YOUR_CLIENT_ID');
  putenv('VR_API_ACCESS_TOKEN=YOUR_ACCESS_TOKEN');

  // You are set! Now you can start making API calls using the wrapper

  // Let's shorten the namespace to make it more usable in the code
  use VerticalResponse\API\VR_APIClient as client;

  /**
   * HOW TO: Handle posible exceptions that might occur while using this wrapper
   * Surround calls to the API in a try..catch block and catch all the CURL and API related exceptions
   */


    /**
     * Example #1: Handle a request made with invalid parameters
     * Surround calls to the API in a try..catch block and catch any VR_API_Error exceptions
     */
    //Let's override one of your credential parameters with an invalid one
    try
    {
      // Let's try to create a new contact with some bad parameters
      $response = client::post(
      	client::ROOT_URL.'contacts/',
    	  array(
      	  'invalid_parameter' => 'Hi! I am not suposed to be here'
      	)
      );
    }
    catch(VerticalResponse\API\VR_API_Error $error)
    {
      // Handle the exception here
      // For this demo, we will print the exception generated
      display($error, 'Example #1: Handle a request made with invalid parameters');
    }

    /**
     * Example #2: Handle a request made with invalid credentials
     * Surround calls to the API in a try..catch block and catch any VR_API_Error exceptions
     */
    //Let's override one of your credential parameters with an invalid one
    putenv('VR_API_CLIENT_ID=invalid');
    try
    {
      // Let's try to get your contact lists, but we know it will fail
      $response = client::get(client::ROOT_URL.'contacts/');
    }
    catch(VerticalResponse\API\VR_API_Error $error)
    {
      // Handle the exception here
      // For this demo, we will print the exception generated
      display($error, 'Example #2: Handle a request made with invalid credentials');
    }

  function display($error, $title)
  {
    //echo 'response: '.print_r($error);
    // Let's print the title followed by a empty line
    echo '<br/>'.$title.'<br/><br/>';
  	// If the object is a CURL_Error exception
    if(is_a($error, 'VerticalResponse\API\CURL_Error'))
  	{
      // Let's print the details of the exception
  	  echo 'Oh no! Something went wrong while attempting to perform the request.';
  	  echo '<br/>';
  	  echo 'Code: '.$error->getCode();
  	  echo '<br/>';
  	  echo 'Message: '.$error->getMessage();
  	  echo '<br/>';
  	  echo 'This exception was thrown in '.$error->getFile().' at line '.$error->getLine();
  	  echo '<br/>';
  	}
  	// If the object is a VR_API_Error exception
  	elseif(is_a($error, 'VerticalResponse\API\VR_API_Error'))
  	{
      // Let's print the details of the exception
  	  echo 'Oh no! An error was detected in the response.';
  	  echo '<br/>';
  	  echo 'Code: '.$error->getCode();
  	  echo '<br/>';
  	  echo 'Message: '.$error->getMessage();
  	  echo '<br/>';
  	  echo 'Request details: '.$error->getMethod().' '.$error->getURL();
  	  echo '<br/>';
  	  echo 'Parameters used: ';
      echo var_dump($error->getParameters());
      echo '<br/>';
      echo 'Failures: ';
      echo var_dump($error->getFailures());
  	  echo '<br/>';
  	  echo 'This exception was thrown in '.$error->getFile().' at line '.$error->getLine();
  	}
  	else
  	{
  	  // If it's not any of the exception classes from above, let's just print it
  	  echo print_r($error).' ('.gettype($error).')';
  	}
    echo '<br/><br/>End of '.$title.'<br/>';
  }


?>
