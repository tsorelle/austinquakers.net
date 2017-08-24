CREATE OR REPLACE VIEW committeeNominationsView AS
  SELECT
    c.committeeId, c.`name` AS committeeName, FormatName(p.`firstName`,p.`middleName`,p.`lastName`) AS member,
    -- cm.status, cm.committeememberId,
     IF(cm.`roleId` > 1, CONCAT('(',cr.`roleName`,')'), '') AS role,
     CASE `status`
        WHEN 1 THEN 'First reading'
        WHEN 2 THEN 'Second reading'
        ELSE ''
     END AS nominiationStatus
  FROM committees c
    JOIN committeemembers cm ON cm.`committeeId` = c.`committeeId`
    JOIN persons p ON p.`personID` = cm.`personId`
    JOIN `committeeroles` cr ON cm.`roleId` = cr.`roleId`
  WHERE cm.status IN (1,2)
  ORDER BY c.name, p.lastname, p.firstname;

