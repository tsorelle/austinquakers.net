DROP VIEW addresspoints
CREATE VIEW addresspoints AS
SELECT
  a.addressID   AS id,
  a.addressName AS `name`,
  a.addressLabel AS addr,
  a.latitude   AS lat,
  a.longitude  AS lon
FROM mapview a