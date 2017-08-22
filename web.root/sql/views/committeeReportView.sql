CREATE OR REPLACE VIEW committeeReportView AS
  SELECT
    c.committeeId, c.name AS committeeName, cm.status AS statusId,
    FormatName(p.firstName,p.middleName,p.lastName) AS member, p.email,
    IF (p.phone IS NULL OR p.phone = '',a.phone,p.phone) AS phone,
    IF(cm.roleId > 1, CONCAT('(',cr.roleName,')'), '') AS role,
    CASE STATUS
        WHEN 1 THEN 'First reading'
        WHEN 2 THEN 'Second reading'
        ELSE ''
    END AS nominiationStatus
  FROM committees c
    JOIN committeemembers cm ON cm.committeeId = c.committeeId
    JOIN persons p ON p.personID = cm.personId
    LEFT OUTER JOIN addresses a ON a.addressId = p.addressId
    JOIN committeeroles cr ON cm.roleId = cr.roleId
  WHERE cm.daterelieved IS NULL
  ORDER BY c.name, cm.status, p.lastname, p.firstname;