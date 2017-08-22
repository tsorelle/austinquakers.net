<?
// TODO: Update for new site
  // prerequisites: standardclasses
  include_once("$fmaRoot/php/includes/mysqldate.php");

  class TCommitteeMemberList {
    var $query;
    var $cid;

    function TCommitteeMemberList($committeeId) {
      $this->cid = $committeeId;
    }

    function select($where)
    {
      $this->query = new TQuery();
      $this->query->execute(
        'SELECT committeeMemberId, firstName, lastName, c.personId, '.
        's.description as status, r.roleName as role, '.
        'startOfService, dateRelieved '.
        'FROM committeemembers c  '.
        'LEFT JOIN persons p on c.personId = p.personId '.
        'LEFT JOIN committeestatus s on c.status = s.statusId '.
        'LEFT JOIN committeeroles r on c.roleId = r.roleId '.
        "WHERE committeeId = $this->cid AND ($where)");
      return ($this->query->getRowCount() > 0);
    }  //  select

    function next()
    {
      if (!isset($this->query))
        return false;
      return ($this->query->next());
    }  //  next

    function getMemberId()
    {
      return $this->query->get('committeeMemberId');
    }  //  getMemberId
    function getMemberName()
    {
      return $this->query->get('firstName').' '.$this->query->get('lastName');
    }  //  getName

    function getPersonId()
    {
      return $this->query->get('personId');
    }  //  getPersonId

    function getRole()
    {
      return $this->query->get('role');

    }  //  getRole

    function getStartDate()
    {
       return  formatMySqlDate(US_DATE_FORMAT, $this->query->get('startOfService'));
    }  //  getStartDate

    function getEndDate()
    {
       return  formatMySqlDate(US_DATE_FORMAT, $this->query->get('dateRelieved'));

    }  //  getEndDate

    function getStatus()
    {
      return $this->query->get('status');

    }  //  getStatus

    function selectCurrent()
    {
       return $this->select('status=3 and dateRelieved is NULL');
    }  //  selectCurrent

    function selectPast()
    {
       return $this->select('status=3 and dateRelieved is not NULL');
    }  //  selectPast

    function selectNominations()
    {
       return $this->select('status=1 or status=2');

    }  //  selectNominations
  }  //  TCommitteeMemberList
?>
