DROP TRIGGER IF EXISTS `tr_direct_debit_ai`;

CREATE TRIGGER `tr_direct_debit_ai` AFTER INSERT
ON `direct_debit` FOR EACH ROW
INSERT INTO  `direct_debit_hist` (`id`,
`organisation_id`,
`person_id`,
`status_id`,
`mandate_reference`,
`unique_identifier`,
`slots`,
`setup_date`,
`next_collection_date`,
`last_increment_date`,
`mot1_legacy_id`,
`created_by`,
`created_on`,
`last_updated_by`,
`last_updated_on`,
`version`,
`batch_number`,
`is_active`)
VALUES (OLD.`id`,
OLD.`organisation_id`,
OLD.`person_id`,
OLD.`status_id`,
OLD.`mandate_reference`,
OLD.`unique_identifier`,
OLD.`slots`,
OLD.`setup_date`,
OLD.`next_collection_date`,
OLD.`last_increment_date`,
OLD.`mot1_legacy_id`,
OLD.`created_by`,
OLD.`created_on`,
OLD.`last_updated_by`,
OLD.`last_updated_on`,
OLD.`version`,
OLD.`batch_number`,
OLD.`is_active`);

DROP TRIGGER IF EXISTS `tr_direct_debit_au`;

CREATE TRIGGER `tr_direct_debit_au` AFTER UPDATE
ON `direct_debit` FOR EACH ROW
INSERT INTO  `direct_debit_hist` (`id`,
`organisation_id`,
`person_id`,
`status_id`,
`mandate_reference`,
`unique_identifier`,
`slots`,
`setup_date`,
`next_collection_date`,
`last_increment_date`,
`mot1_legacy_id`,
`created_by`,
`created_on`,
`last_updated_by`,
`last_updated_on`,
`version`,
`batch_number`,
`is_active`)
VALUES (OLD.`id`,
OLD.`organisation_id`,
OLD.`person_id`,
OLD.`status_id`,
OLD.`mandate_reference`,
OLD.`unique_identifier`,
OLD.`slots`,
OLD.`setup_date`,
OLD.`next_collection_date`,
OLD.`last_increment_date`,
OLD.`mot1_legacy_id`,
OLD.`created_by`,
OLD.`created_on`,
OLD.`last_updated_by`,
OLD.`last_updated_on`,
OLD.`version`,
OLD.`batch_number`,
OLD.`is_active`);

DROP TRIGGER IF EXISTS `tr_direct_debit_ad`;

CREATE TRIGGER `tr_direct_debit_ad` AFTER DELETE
ON `direct_debit` FOR EACH ROW
INSERT INTO  `direct_debit_hist` (`id`,
`organisation_id`,
`person_id`,
`status_id`,
`mandate_reference`,
`unique_identifier`,
`slots`,
`setup_date`,
`next_collection_date`,
`last_increment_date`,
`mot1_legacy_id`,
`created_by`,
`created_on`,
`last_updated_by`,
`last_updated_on`,
`version`,
`batch_number`,
`is_active`)
VALUES (OLD.`id`,
OLD.`organisation_id`,
OLD.`person_id`,
OLD.`status_id`,
OLD.`mandate_reference`,
OLD.`unique_identifier`,
OLD.`slots`,
OLD.`setup_date`,
OLD.`next_collection_date`,
OLD.`last_increment_date`,
OLD.`mot1_legacy_id`,
OLD.`created_by`,
OLD.`created_on`,
OLD.`last_updated_by`,
OLD.`last_updated_on`,
OLD.`version`,
OLD.`batch_number`,
OLD.`is_active`);