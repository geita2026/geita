-- Load the official 2026 DITF judging categories into SAMS.
-- Run against the SAMS database (local: mis_update).
-- Deactivates the old list and upserts the 2026 set with shared IDs.

UPDATE `judging_categories` SET `categoryStatus` = 0;

INSERT INTO `judging_categories` (`categoryId`,`categoryName`,`description`,`categoryStatus`,`createdAt`,`updatedAt`) VALUES
('05385de0-7d5c-5b72-9386-e23cff76c378','Machinery, Equipment and Technology Exhibitor','',1,NOW(),NULL),
('2a777667-c4f3-5aac-89e9-75e4e6581e50','Agriculture, Fishing and Livestock Products Exhibitor','',1,NOW(),NULL),
('caeeb5a6-18b1-5c7d-b650-65daceadfe4f','Food and Beverages Processors Exhibitor','',1,NOW(),NULL),
('27a37b8b-17fe-501b-b71f-bdf323a76760','Furniture & Interior Decoration Exhibitor','',1,NOW(),NULL),
('65fc828c-cc39-5934-b8ba-e1f2116ea9ad','Transport and Logistics Exhibitor','',1,NOW(),NULL),
('aaeabc85-ed63-5af8-93ae-4e3cce8b6e60','Trade and Investment Facilitation Exhibitor','',1,NOW(),NULL),
('41e3852f-4767-557b-9424-60ba2073dca5','Ministries Exhibitor','',1,NOW(),NULL),
('cf2feeba-60dd-5d8e-9c0f-c3cab513e954','Government Institutions Exhibitor','',1,NOW(),NULL),
('0260baec-65f2-59ea-a76c-e325934f87f4','Banks and Financial Institutions Exhibitor','',1,NOW(),NULL),
('cf48c5fb-a0ec-585d-9a8a-be82d103e2f2','Telecommunication Exhibitor','',1,NOW(),NULL),
('a764d121-38d0-5634-adba-e10124842f3e','Innovation and Solutions Exhibitor','',1,NOW(),NULL),
('901cedd3-c271-587d-9268-e1078342563d','Health and Medical Services Providers Exhibitor','',1,NOW(),NULL),
('e8b1f9a8-e780-5727-90ee-22c865c4abb7','Insurance and Social Schemes Exhibitor','',1,NOW(),NULL),
('16b7f704-ca58-5653-bb29-36339664fa11','Higher Learning, Skills Development and Research Institution Exhibitor','',1,NOW(),NULL),
('e657be3b-e3b4-5ea6-8d37-66c84a2c64e4','Regulatory Bodies Exhibitor','',1,NOW(),NULL),
('c94bfa8a-a5c1-5d60-89ed-84e97523634f','Local Government Authorities (LGA''s) Exhibitor','',1,NOW(),NULL),
('1b9e54be-b4e3-52ff-a09c-58bf8c7c2f98','Real Estates Exhibitor','',1,NOW(),NULL),
('5998e787-5007-5c90-8aaa-d0c0c0a19a88','Construction and Building Materials Exhibitor','',1,NOW(),NULL),
('74c4e2ef-4c08-5381-af14-ce12b3fd295d','Best Women in Trade Exhibitor','',1,NOW(),NULL),
('bb9a715c-fe3d-5d8a-afad-f64bba4a7d81','Best Zanzibar Exhibitor','',1,NOW(),NULL),
('33567573-e816-5d76-9411-6b9053945034','Best Tanzania SMEs Exhibitor','',1,NOW(),NULL),
('98692c3d-18c6-5290-a1be-629a8f597ea3','Best Start Ups Exhibitor','',1,NOW(),NULL),
('5cc6a8cf-6135-5771-90cc-48793217846a','Best Media Exhibitor','',1,NOW(),NULL),
('f3b44aad-1c29-5987-9d1c-657e48215ec3','Best Foreign Exhibitor','',1,NOW(),NULL),
('5380a983-4f64-5456-badc-d5a227b76420','Best Tanzanian Manufacturer Exhibitor','',1,NOW(),NULL),
('a86e59d1-03e3-57da-a7fa-c15ccf083fa2','Best Tanzania Branded Pavilion','',1,NOW(),NULL),
('203e62d6-d6b9-5661-a433-6616497a1b98','Best Foreign Country Pavilion','',1,NOW(),NULL)
ON DUPLICATE KEY UPDATE `categoryName`=VALUES(`categoryName`), `categoryStatus`=1, `updatedAt`=NOW();
