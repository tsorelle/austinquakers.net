<?php

/** Class: TApproveMembership ***************************************/
/// post membership approval
/************************************************************/
class TDoImport extends TPageAction {

    private $testrun = false;
    private $lastOrg = '';
    private $lastMid = -1;
    private $fakeMid = 1;
    private $fakePid = 1;
    private $membershipTable = '<table border="1"><tr><th>MID</th><th>Name</th> <th>Type</th> <th>website</th><th>startDate</th> <th>expiration</th> <th>applicationStatus</th> <th>active</th></tr>';
    private $contactTable =    '<table border="1"><tr><th>MID</th> <th>pID</th> <th>firstName</th> <th>lastName</th><th>address1</th> <th>address2</th> <th>city</th> <th>state</th><th>postalCode</th> <th>country</th> <th>phone</th> <th>phone2</th><th>email</th>  <th>active</th></tr>';

    private function cleanup($s) {
        return str_replace('"', '', trim($s));
    }


    private function addContact($contact) {
        if ($this->testrun) {

            $pid = $this->fakePid++;

        }
        else {
            $contact->add();
            $pid = $contact->getId();
        }
        $this->contactTable .=
            sprintf('<tr><td>%d</td><td>%d</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>',
                $contact->getMembershipId(),
                $pid,
                $contact->getFirstName(),
                $contact->getLastName(),
                $contact->getAddress1(),
                $contact->getAddress2(),
                $contact->getCity(),
                $contact->getState(),
                $contact->getPostalCode(),
                $contact->getCountry(),
                $contact->getPhone(),
                $contact->getPhone2(),
                $contact->getEmail(),
                $contact->getActive());

        return $pid;
    }

    private function addMembership($membership) {
        if ($this->testrun) {
            $mid = $this->fakeMid++;
        }
        else {
            $membership->add();
            $mid = $membership->getId();
        }
        $this->membershipTable .=
            sprintf('<tr><td>%s</td><td>%s</td><td>%d</td><td>%s</td><td>%s</td><td>%s</td><td>%d</td><td>%d</td></tr>',
                  $mid, // MID
                  $membership->getMembername(),
                  $membership->getMembershipType(),
                  $membership->getWebsite(),
                  $membership->getStartDate(),
                  $membership->getExpiration(),
                  $membership->getApplicationStatus(),
                  $membership->getActive());
        $this->lastMid = $mid;
        return $mid;
    }

    protected function run() {
        $path = $_SERVER['DOCUMENT_ROOT'] . '/tops/site_lib/applications/contacts/data/';
        $imports = file($path . 'quipcontacts.txt');
        $logfile =  fopen($path.'imports.txt', 'w');




        $table = '<table border="1"><tr><th>count</th><th>Type</th><th>MID</th><th>First Name</th><th>	Last Name</th><th>	Organization</th><th>	Address1</th><th>	Address2</th> <th>	City</th><th>state</th><th>		Post Code</th><th>	Country</th><th>	Phone</th><th>	Phone2</th><th>	Email</th><th>Expires</th><th>web</th></tr>';
        foreach ($imports as $line) {
            //echo $line.'<br>';
            $formData = new stdClass();
            $formData->membershipId = 0;
            $formData->categories = 0;
            $formData->membershipType =  0;
            $formData->memberName = '';
            $formData->website = '';
            $formData->notes = '';
            $formData->expiration = '';



            $record = split("\t", $line);
            $count = sizeof($record);
            if ($count > 13) {
                $firstName = $this->cleanup($record[0]);
                $lastName = $this->cleanup($record[1]);
                $organization = $this->cleanup($record[2]);
                $address1 = $this->cleanup($record[3]);
                $address2 = $this->cleanup($record[4]);
                $city = $this->cleanup($record[5]);
                $state = $this->cleanup($record[6]);
                $postCode = $this->cleanup($record[7]);
                $country = $this->cleanup($record[8]);
                $phone = $this->cleanup($record[9]);
                $phone2 = $this->cleanup($record[10]);
                $email = $this->cleanup($record[11]);
                $year = $this->cleanup($record[12]);
                $website = $this->cleanup($record[13]);

                $expiration = ($year == 'x') ? '2009-04-30':'2010-04-30';
                $startDate = '2007-01-01';



                $memberType = empty($organization) ? 1 : 2;
                $memberName = ($memberType == 1) ? $firstName.' '.$lastName : $organization;

                if ($memberType == 2 && $organization == $this->lastOrg) {
                    $mid = $this->lastMid;
                }
                else {

                    $membership = new TMembership();
                    $membership->setMembername($memberName);
                    $membership->setMembershipType($memberType);
                    $membership->setStartDate($startDate);
                    $membership->setExpiration( $expiration);

                    $membership->setWebsite($website);
                    $membership->setApplicationStatus(2);
                    $membership->setNotes('');
                    $membership->setActive(1);
                    $mid = $this->addMembership($membership);
                    $this->lastMid = $mid;
                }
                $this->lastOrg = $organization;

                $contact = new TPerson();
                if (empty($firstName) && empty($lastName)) {
                    $firstName = 'QUIP';
                    $lastName = 'Contact';
                }

                $contact->setFirstName($firstName);
                $contact->setLastName( $lastName);
                $contact->setMembershipId($mid);
                $contact->setAddress1($address1);
                $contact->setAddress2($address2 );
                $contact->setCity($city );
                $contact->setState( $state);
                $contact->setPostalCode($postCode );
                $contact->setCountry($country);
                $contact->setPhone($phone );
                $contact->setPhone2($phone2 );
                $contact->setEmail( $email );
            	$contact->setActive(1);
                $pid = $this->addContact($contact);
                fwrite($logfile, "$pid\r\n");

            }
        }
        fclose($logfile);
       $this->pageController->addMainContent($this->membershipTable .'</table>');
        $this->pageController->addMainContent($this->contactTable . '</table>');
//        rename($path . 'quipcontacts.txt', $path . 'processed_quipcontacts.txt');
}

}