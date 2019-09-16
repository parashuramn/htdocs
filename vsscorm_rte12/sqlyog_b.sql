`scormvars_full_data`TRUNCATE `scormvars_full_data`

SELECT * FROM scormvars WHERE SCOInstanceID =1

SHOW CREATE TABLE `scormvars`.`scormvars_full_data`; 

SELECT JSON_EXTRACT(VALUE,'$.actor.mbox') FROM `scormvars_full_data` WHERE params_key ='Statement';
SELECT JSON_EXTRACT('{"id": 14, "SampleKey": "SampleValue"}', '$.SampleKey');
SELECT VALUE FROM `scormvars_full_data` WHERE params_key='Statement'
DELETE FROM `scormvars_full_data` WHERE id > 2606
UPDATE `scormvars_full_data` SET activity_id = 'http://nss-grassblade.com/wordpress/gb_xapi_content/fmla' WHERE id >= 3114
UPDATE `scormvars_full_data` SET result='failed',registration = '3' WHERE id >= 3114
 
UPDATE `scormvars_full_data` SET COMMENT='The Really Difficult Puzzle Quiz', mastery_score=50 WHERE activity_id='http://nss-grassblade.com/wordpress/gb_xapi_content/PuzzleQuizSCORMExport'
UPDATE `scormvars_full_data` SET COMMENT='Captivate 1.2', mastery_score=50 WHERE activity_id='http://nss-grassblade.com/wordpress/gb_xapi_content/democaptivate1.2'
UPDATE `scormvars_full_data` SET COMMENT='iSpring 1.2', mastery_score=50 WHERE activity_id='http://nss-grassblade.com/wordpress/gb_xapi_content/ispring1.2'

SELECT * FROM `scormvars_full_data` WHERE activity_id='http://nss-grassblade.com/wordpress/gb_xapi_content/PuzzleQuizSCORMExport' AND result ='failed'
SELECT * FROM `scormvars` WHERE SCOInstanceID=10 AND varName='cmi.lesson_status'
UPDATE `scormvars_full_data` SET EVENT=COMMENT WHERE id >= 3308
UPDATE `scormvars_full_data` SET result='completed',COMMENT='iSpringSCORM1.2QuizwithSurvey', mastery_score=50 WHERE id >= 3470

UPDATE `scormvars_full_data` SET result='completed' WHERE id >= 3308 AND id < 3470

SELECT * FROM `scormvars_full_data` WHERE  activity_id='http://nss-grassblade.com/wordpress/gb_xapi_content/PuzzleQuizSCORMExport'
