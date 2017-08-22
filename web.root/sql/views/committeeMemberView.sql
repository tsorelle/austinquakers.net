CREATE OR REPLACE VIEW committeeMemberView AS
  SELECT  cm.committeeMemberId, cm.committeeId, FormatName(firstName,middleName,lastName) AS 'name', p.personId, p.email,
    IF (p.phone IS NULL OR p.phone = '',a.phone,p.phone) AS phone,
    cm.roleId, IF(cm.roleId = 1,'', CONCAT('(', cr.roleName,')')) AS role,
    cs.description AS 'status', cm.status AS statusId,
    cm.startOfService, cm.endOfService, cm.dateRelieved,
    cm.notes, cm.dateAdded, cm.dateUpdated
  FROM committeemembers cm
    JOIN persons p ON p.personID = cm.personId
    LEFT OUTER JOIN addresses a ON a.addressId = p.addressId
    JOIN committeeroles cr ON cr.roleId = cm.roleId
    JOIN committeestatus cs ON cs.statusId = cm.status
  ORDER BY cm.committeeId, p.lastName,p.firstName;

SELECT * FROM committeeMemberView;