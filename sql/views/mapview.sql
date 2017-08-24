ALTER VIEW mapview AS
SELECT DISTINCT
a.addressID   ,      
a.addressName ,
a.address1    ,   
a.address2    ,   
a.city        ,   
a.state       ,   
a.postalCode  ,   
a.country     ,   
a.phone       , 
CONCAT(
    IF (ISNULL(a.`address1`),'',TRIM(a.address1))
   
    , IF(ISNULL(a.address2) || TRIM(a.`address2`) = '',
        '',
        -- else
        CONCAT(' ',TRIM(a.address2)))
     
     , IF ((a.`city` = 'Austin' AND (a.`state` = 'TX' OR a.`state` = 'Texas')),', Austin', 
        -- else
        CONCAT(
            IF (ISNULL(a.city),'', 
            -- else
                CONCAT(', ',TRIM(a.city))), 
                    IF (ISNULL(a.`state`),'',
                    -- else
                    CONCAT(' ',TRIM(a.state)))))
       , IF(ISNULL(a.`country`) || a.`country` = 'United States' || a.`country` = 'US' || a.`country` = 'USA','',CONCAT(' ',a.`country`))
    ) -- end concat
    AS addresslabel
,b.latitude,b.longitude
FROM addresses a
LEFT OUTER JOIN addresslocations b ON a.addressId = b.addressId 
LEFT OUTER JOIN persons p ON p.addressID = a.addressID 
GROUP BY a.addressid,a.active,a.directoryCode,p.personID HAVING ((COUNT(p.deceased) = 0) OR (COUNT(p.deceased) <>  COUNT(p.personID)))
AND (a.active = 1 AND a.directoryCode > 0)
