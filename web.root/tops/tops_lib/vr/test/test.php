<?php
  // Let's load the required scripts for this file
  require_once(__DIR__ . DIRECTORY_SEPARATOR . 'vr_api_test.php');

  /**
   * DISCLAIMER! (Please read this first)
   * The following PHP script demonstrates the capabilities of the Test class defined in the test folder.
   * The goal of this wrapper is to help you get started with the VR API. The wrapper provides 
   * insights into connecting and making VR API calls. You can extend this and create your own 
   * custom application. The wrapper does not cover all the API calls VR provides. For a full 
   * list of API calls VR provides, please refer to the documentation. This software is 
   * provided "as-is", please note that VerticalResponse will not maintain or update this.
   */

  /**
   * This file provides examples of all the methods in the Test class of this wrapper
   */

  // First a little configuration:
  // Set your VR API credentials
  putenv('VR_API_CLIENT_ID=YOUR_CLIENT_ID');
  putenv('VR_API_ACCESS_TOKEN=YOUR_ACCESS_TOKEN');

  // You are set! Now you can start making API calls using the wrapper

  // Let's create a contact
  $contact = VerticalResponse\API\Test::create_contact(
  	array(
  	  'email' => 'dummy_contact_'.time().'@verticalresponse.com'
  	)
  );
  // Let's display the response of the request using the display_response function defined at the end of this file
  display_response($contact, 'Create a contact');

  // Let's see your contacts
  $contacts = VerticalResponse\API\Test::get_contacts();
  display_response($contacts, 'List your contacts');

  // Let's get the details of one of your contacts
  $contact = $contacts->items[0];
  $details = VerticalResponse\API\Test::get_contact_details($contact);
  display_response($details, 'Get the details of one of your contacts');

  // Let's create a list
  $list = VerticalResponse\API\Test::create_list(
    array(
      'name' => 'Dummy list'.time()
    )
  );
  display_response($list, 'Create a list');

  // Let's see all your lists
  $lists = VerticalResponse\API\Test::get_lists();
  display_response($lists, 'Get your lists');

  // Let's see the details of one of your lists
  // Let's use the lists object of the previous example ($lists)
  $list = $lists->items[0];
  $details = VerticalResponse\API\Test::list_details($list);
  display_response($details, 'Get details of one of your lists');

  // Let's see the contacts that belong to one of your lists
  // Let's use the list object of the previous example ($list)
  $contacts = VerticalResponse\API\Test::get_lists_contacts($list);
  display_response($contacts, 'Get the contacts of one of your lists');

  // Let's create a new contact in one of your lists
  // Let's use the list object of the previous example ($list)
  $contact = VerticalResponse\API\Test::create_list_contact(
  	array(
  	  'email' => 'dummy_list_contact_'.time().'@verticalresponse.com'
  	), $list
  );
  display_response($contact, 'Create a contact in a list');


  // This method is used to format the output of the examples above
  function display_response($response, $title)
  {
    // Let's print the title followed by a empty line
    echo '<br/>'.$title.'<br/><br/>';
    echo print_r($response);
    echo '<br/><br/>End of '.$title.'<br/>';
  }

?>
