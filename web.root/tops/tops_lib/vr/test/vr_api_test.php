<?php
  namespace VerticalResponse\API;
  // Let's load the required scripts for this file
  require_once(__DIR__ . DIRECTORY_SEPARATOR . '../vr_api_contact_list.php');

  /**
   * Really simple class for testing and demonstrating purposes of this wrapper
   * This class is a simplified version of this wrapper, and takes the minimum parameters needed to perform a successful request for each action
   */
  class Test
  {

    // Returns you contact list
    public function get_contacts()
    {
      return Contact::all();
    }

    // Creates a contact
    // Argument: a parameters array
    public function create_contact($parameters)
    {
      return Contact::create($parameters);
    }

    // Gets the details of a contact
    // Argument: a contact object
    public function get_contact_details($contact)
    {
      return $contact->details();
    }

    // Returns your contact lists
    public function get_lists()
    {
      return ContactList::all();
    }

    // Creates a new list
    // Argument: a parameters array
    public function create_list($parameters)
    {
      return ContactList::create($parameters);
    }

    // Returns the details of a list
    // Argument: a ContactList object
    public function list_details($list)
    {
      return $list->details();
    }

    // Returns the contacts that belongs to a list
    // Argument: a ContactList object
    public function get_lists_contacts($list)
    {
      return $list->contacts();
    }

    // Creates a contact in a specific list
    // Arguments: a parameters array to create a contact, a ContactList object
    public function create_list_contact($parameters, $list)
    {
      return $list->create_contact($parameters);
    }

  }
?>
