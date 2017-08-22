<?
/** Class: TSubscriptions ***************************************/
/// handles data access for elists and emails tables
/**
*****************************************************************/
class TSubscriptions
{
    public function __construct() {
    }

    public function __toString() {
        return 'TSubscriptions';
    }

    public static function GetRecipientInfo($pid) {
        $result = new StdClass();
        $result->pid = $pid;

        $sql = 'select a.addressId, p.firstName, p.lastName, p.email, a.fnotes '.
                'from persons p left outer join addresses a on p.addressId = a.addressId '.
                'where personId = ?';

        $statement = TSqlStatement::ExecuteQuery($sql, 'i', $pid);  ;
        $statement->instance->bind_result($addressId,$firstName, $lastName, $email, $fnByMail);
        if ($statement->next()) {
            $result->personName = $firstName.' '.$lastName;
            $result->addressId = $addressId;
            $result->email = $email;
            $result->fnByMail = $fnByMail;
        }
        else
            return false;
        return $result;
    }

    public static function GetSubscriptions($pid) {
        $result = array();
        $sql = 'select 	el.elistId, el.listCode, el.listName, em.personId, em.altEmail '.
            'from elists el left outer join emails em on  el.elistId = em.listId '.
            'and em.PersonId = ?';
        $statement = TSqlStatement::ExecuteQuery($sql, 'i', $pid);  ;
        $statement->instance->bind_result($elistId,$listCode,$listName, $personId, $altEmail);
        while ($statement->next()) {
            $item = new StdClass();
            $item->elistId  = $elistId;
            $item->listCode = $listCode;
            $item->listName = $listName;
            $item->altEmail = $altEmail;
            $item->selected = (!empty($personId));
            array_push($result,$item);
        }
        return $result;
    }

    public static function UpdatePersonInfo($pid, $aid, $email, $fnByMail) {
        $sql = "UPDATE persons SET email = ? WHERE personID = ?";
        TSqlStatement::ExecuteNonQuery($sql,'si',$email, $pid);
        if (!empty($aid)) {
            $fnByMail = empty($fnByMail) ? 0 : 1;
            $sql = "UPDATE addresses SET fnotes = ? WHERE addressID = ?";
            TSqlStatement::ExecuteNonQuery($sql,'ii',$fnByMail, $aid);
        }
    }


    public static function UpdateSubscriptions($pid, $subscriptions) {
        $sql = "DELETE FROM emails where personId = ?";
        TSqlStatement::ExecuteNonQuery($sql,'i',$pid);
        foreach ($subscriptions as $subscription) {
            if ($subscription->selected) {
                $sql = "INSERT INTO emails values (?,?,?)";
                TSqlStatement::ExecuteNonQuery($sql,'iis',$pid,$subscription->elistId,$subscription->altEmail);
            }
        }
    }
}
// end TSubscriptions