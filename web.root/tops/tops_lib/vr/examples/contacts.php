<?php
  // Let's load the required scripts for this file
  require_once(__DIR__ . DIRECTORY_SEPARATOR . '../vr_api_contact.php');

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
   * This file provides examples of the different contact-related operations you can perform with this wrapper
   * In this file we will show you
   * > HOW TO: Make direct requests to the API
   *   Example #1: Make a POST request to create a contact
   *   Example #2: Make a GET request to get all of your contacts
   * > HOW TO: Make object oriented requests to the API
   *   Example #3: Make an object oriented request to get all of your contacts
   *   Example #4: Get the details from your first contact in your contact list (object-oriented)
   *   Example #5: Make an object oriented request to get your contact list with pagination
   *   Example #6: Make an object oriented request to create a contact
   */

  // First a little configuration:
  // Set your VR API credentials
  putenv('VR_API_CLIENT_ID=YOUR_CLIENT_ID');
  putenv('VR_API_ACCESS_TOKEN=YOUR_ACCESS_TOKEN');

  // You are set! Now you can start making API calls using the wrapper

  // Let's shorten the namespace to make it more usable in the code
  use VerticalResponse\API\Contact as contact;
  
  /** 
   * HOW TO: Make direct requests to the API
   * Use the methods .get and .post defined in the Contact class
   */
    
    /** 
     * Example #1: Make a POST request to create a contact
     * TIP: You can use the constant ROOT_URL that is the base URL portion for all calls to the VR API
     */
    $response = contact::post(
    	contact::ROOT_URL.'contacts/',
    	array(
    	  'email' => 'test_contact_1'.time().'@verticalresponse.com'
    	)
    );
    // Let's use the display_response function defined in the bottom of this file
    // TIP: You can also use var_dump($response) instead of this function
    // Notice that the response is returned in the form of an associative array
    display_response($response, 'Example #1: Make a POST request to create a contact');

    /** 
     * Example #2: Make a GET request to get all of your contacts
     */
    $response = contact::get(contact::ROOT_URL.'contacts/');
    // Let's print the contacts of the response
    // Notice that your contacts are returned in the form of an associative array
    display_response($response['items'], 'Example #2: Make a GET request to get all of your contacts');

  /**
   * HOW TO: Make object oriented requests to the API
   * Use the methods .all(), .create() and .details() defined in the Contact class
   */

    /** 
     * Example #3: Make an object oriented request to get all of your contacts
     * TIP: You can provide the type parameter in this request: Making a basic, standard or all request.
     */
    // Let's perform an request with the type parameter basic
    $response = contact::all(array('type' => 'basic'));
    // Let's print the response
    // Notice that the $response variable is a Response object
    // And the items is an array of Contact objects
    display_response($response, 'Example #3: Make an object oriented request to get all of your contacts');

    /**
     * Example #4: Get the details from your first contact in your contact list (object-oriented)
     * Use the .details method defined in the Contact class
     */
    // Let's use the $response object from the last example
    // Get the first contact of the $response object
    $contact = $response->items[0];
    // Let's print the details of the contact
    display_response($contact->details(), 'Example #4: Get the details from your first contact in your contact list (object-oriented)');

    /**
     * Example #5: Make an object oriented request to get your contact list with pagination
     * To use pagination, simply specifify the index and limit parameters in the call
     */
    // Let's get you contact list paginated
    $response = contact::all(array('index' => 2, 'limit' => 5));
    display_response($response, 'Example #5: Make an object oriented request to get your contact list with pagination');

    /**
     * Example #6: Make an object oriented request to create a contact
     */
    $response = contact::create(
      array('email' => 'test_contact_2'.time().'@verticalresponse.com')
    );
    display_response($response, 'Example #6: Make an object oriented request to create a contact');

  
  function display_response($response, $title)
  {
    // Let's print the title followed by a empty line
    echo '<br/>'.$title.'<br/><br/>';
  	echo print_r($response);
    echo '<br/><br/>End of '.$title.'<br/>';
  }
  
?>
