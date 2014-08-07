SELECT
	tblFoDomain.id as 'id',
	CONVERT(tblFoDomain.des USING utf8) as 'name',
	NOW() as 'created_at',
	NOW() as 'updated_at',
	NULL as 'deleted_at'
FROM tblFoDomain;

SELECT
	tblFoLocation.id AS 'id',
	tblFoLocation.des AS 'name',
	IF(tblFoAddress.`municipalityName` <> '', tblFoAddress.`municipalityName`, NULL) AS 'city',
	IF(LENGTH(tblFoAddress.provinceCode) = 2, tblFoAddress.provinceCode, IF(tblFoAddress.provinceCode = 'QUEBEC', 'QC', NULL)) AS 'state',
	IF (tblFoAddress.countryName <> '', SUBSTRING(tblFoAddress.countryName, 1, 2), NULL) AS 'country',
	NOW() as 'created_at',
	NOW() as 'updated_at',
	1 as 'user_id',
	IF(tblFoAddress.`civicNo` OR tblFoAddress.`streetName`, CONCAT(tblFoAddress.`civicNo`, ' ', tblFoAddress.`streetName`), NULL) AS 'address',
	IF(tblFoAddress.`suiteNumber`, CONCAT('Suite ', tblFoAddress.`suiteNumber`), NULL) AS 'address2',
	IF(tblFoAddress.`postalCode` <> '', tblFoAddress.`postalCode`, NULL) AS 'zip',
	NULL as 'deleted_at',
	tblFoLocation.domainId AS 'domain_id'
FROM tblFoLocation
LEFT JOIN tblFoAddress ON tblFoLocation.`address` = tblFoAddress.id;

SELECT
  tblFoBox.id as 'id',
  tblFoBox.des as 'name',
  tblFoHardware.mac as 'asset_tag',
  tblFoHardware.typeId as 'model_id',
  NULL as 'serial',
  NULL as 'purchase_date',
  NULL as 'purchase_cost',
  NULL as 'order_number',
  0 as 'assigned_to',
  NULL as 'notes',
  1 as 'user_id',
  NOW() as 'created_at',
  NOW() as 'updated_at',
  1 as 'physical',
  NULL as 'deleted_at',
  IF(tblFoBox.locationId = 8, 6, 7) as 'status_id',
  0 as 'archived',
  NULL as 'warranty_months',
  0 as 'depreciated',
  NULL as 'supplier_id',
  0 as 'requestable',
  tblFoBox.locationId as 'location_id'
FROM tblFoBox
INNER JOIN tblFoHardware ON tblFoBox.mac = tblFoHardware.mac;


SELECT
	tblFoHardwareType.id as 'id',
	tblFoHardwareType.des as 'name',
	NULL as 'modelno',
	NULL as 'manufacturer',
	NULL as 'category_id',
	NOW() as 'created_at',
	NOW() as 'updated_at',
	1 as 'deprecation_id',
	1 as 'user_id',
	0 as 'eol'
FROM tblFoHardwareType;