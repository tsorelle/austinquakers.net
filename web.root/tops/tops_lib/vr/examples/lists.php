<?php
  // Let's load the required scripts for this file
  require_once(__DIR__ . DIRECTORY_SEPARATOR . '../vr_api_contact_list.php');

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
   * This file provides examples of the different list-related operations you can perform with this wrapper
   * In this file we will show you
   * > HOW TO: Make direct requests to the API
   *   Example #1: Make a POST request to create a new list
   *   Example #2: Make a GET request to get your lists
   * > HOW TO: Make object oriented requests to the API
   *   Example #3: Make an object oriented request to create a new list
   *   Example #4: Make an object oriented request to get all of your lists
   *   Example #5: Make an object oriented request to get all of your lists with pagination
   *   Example #6: Get the details from your first list (object-oriented)
   *   Example #7: Make an object oriented request to get all the contacts that belong to a particular list
   *   Example #8: Make an object oriented request to create a contact in a particular list
   */

  // First a little configuration:
  // Set your VR API credentials
  putenv('VR_API_CLIENT_ID=YOUR_CLIENT_ID');
  putenv('VR_API_ACCESS_TOKEN=YOUR_ACCESS_TOKEN');

  // You are set! Now you can start making API calls using the wrapper

    // Let's shorten the namespace to make it more usable in the code
  use VerticalResponse\API\ContactList as contactList;

  /** 
   * > HOW TO: Make direct requests to the API
   * Use the methods .get() and .post() defined in the List class
   */
    

    /** 
     * Example #1: Make a POST request to create a new list
     * TIP: You can use the constant ROOT_URL that is the base URL portion for all calls to the VR API
     */
    echo "Test";
    $response =  contactList::post(
      contactList::ROOT_URL.'lists/',
      array(
        'name' => 'Test list'.time().'1'
      )
    );
    // Let's use the display_response function defined in the bottom of this file
    // TIP: You can also use var_dump($response) instead of this function
    // Notice that the list response is returned in the form of an associative array
    display_response($response, 'Example #1: Make a POST request to create a new list');

    /** 
     * Example #2: Make a GET request to get your lists
     * TIP: You can use the constant ROOT_URL that is the base URL portion for all calls to the VR API
     */
    $response = contactList::get(
      contactList::ROOT_URL.'lists/',
      array('type' => 'basic')
    );
    // Notice that the response is returned in the form of an associative array
    display_response($response, 'Example #2: Make a GET request to get your lists');
    

  /**
   * > HOW TO: Make object oriented requests to the API
   * Use the methods .all(), .contacts(), .create_contact(), .details() defined in the List class
   */

    /** 
     * Example #3: Make an object oriented request to create a new list
     * Use the .create() method defined in the lists class
     */
    $response = contactList::create(
      array(
        'name' => 'Test list'.time().'2'
      )
    );
    // Notice that the response is a Response object
    display_response($response, 'Example #3: Make an object oriented request to create a new list');

    /** 
     * Example #4: Make an object oriented request to get all of your lists
     * Use the .all() method defined in the lists class
     * TIP: You can specify the type parameter in this request to perform a basic, standard or all request.
     */
    $response = contactList::all(
      array('type' => 'basic')
    );
    // Notice that the your lists response is returned as a Response object
    display_response($response, 'Example #4: Make an object oriented request to get all of your lists');

    /** 
     * Example #5: Make an object oriented request to get all of your lists with pagination
     * For pagination, you need to specify the index and limit parameters
     */
    $response = contactList::all(
      array('index' => 1, 'limit' => 2)
    );
    // Notice that the your lists response is returned as a Response object
    display_response($response, 'Example #5: Make an object oriented request to get all of your lists with pagination');

    /** 
     * Example #6: Get the details from your first list (object-oriented)
     * Call the .details method() in a List object
     */
    // Let's get all your lists
    $lists = contactList::all();
    // Let's get the details of your first list
    // Notice that items is an array of List objects
    $response = $lists->items[0]->details();
    // Notice that the your lists response is returned as a Response object
    display_response($response, 'Example #6: Get the details from your first list (object-oriented)');

    /** 
     * Example #7: Make an object oriented request to get all the contacts that belong to a particular list
     * Call the .contacts method() in a List object
     */
    // Let's get all your lists
    $lists = contactList::all();
    // Let's get the contacts that belong to your first list
    // Notice that items is an array of List objects
    $response = $lists->items[0]->contacts();
    // Notice that the items of the response are returned as a Contact object
    display_response($response, 'Example #7: Make an object oriented request to get all the contacts that belong to a particular list');

    /** 
     * Example #8: Make an object oriented request to create a contact in a particular list
     * Call the .create_contact method() in a List object
     */
    // Let's get all your lists
    $lists = contactList::all();
    // Let's get the contacts that belong to your first list
    // Notice that items is an array of List objects
    $response = $lists->items[0]->create_contact(
      array(
        'email' => 'contact_list'.time().'3'.'@verticalresponse.com'
      )
    );
    // Notice that the the response is returned as a Contact object
    display_response($response, 'Example #8: Make an object oriented request to create a contact in a particular list');

  function display_response($response, $title)
  {
    // Let's print the title followed by a empty line
    echo '<br/>'.$title.'<br/><br/>';
    echo print_r($response);
    echo '<br/><br/>End of '.$title.'<br/>';
  }

?>
