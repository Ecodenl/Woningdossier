### Corrections for fixing the messed up accounts ID on Wijdemeren (and NHEC)

```sql
SET foreign_key_checks = FALSE;

UPDATE accounts SET id = id + 82000 WHERE id >= 12207 AND id < 94207;


DELIMITER $$

DROP PROCEDURE IF EXISTS fixBuildings $$
DROP PROCEDURE IF EXISTS loopBuildings $$

CREATE PROCEDURE fixBuildings(buildingId INT)
BEGIN

	DECLARE done INT DEFAULT FALSE; 
	DECLARE tableName VARCHAR(64);
	
	DECLARE tableCursor CURSOR FOR
        SELECT DISTINCT TABLE_NAME
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE COLUMN_NAME = 'account_id';
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN tableCursor;
    
    read_loop: LOOP
        FETCH tableCursor INTO tableName;
		IF done THEN
			LEAVE read_loop;
        END IF;
        
        SET @qry = CONCAT('UPDATE ', tableName, ' SET account_id = ', buildingId, ' WHERE account_id = ', buildingId, ' - 82000');
        PREPARE qry FROM @qry;
        EXECUTE qry;
    END LOOP;
    CLOSE tableCursor;
END$$

CREATE PROCEDURE loopBuildings()
BEGIN 
    DECLARE done INT DEFAULT FALSE; 
	DECLARE buildingId INT;

    DECLARE buildingCursor CURSOR FOR
        SELECT id FROM accounts WHERE id >= 12207;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN buildingCursor;

    read_loop: LOOP 
        FETCH buildingCursor INTO buildingId;
        IF done THEN
            LEAVE read_loop;
        END IF;

        CALL fixBuildings(buildingId);
    END LOOP;
    CLOSE buildingCursor;
END$$

CALL loopBuildings()$$

DROP PROCEDURE IF EXISTS fixBuildings $$
DROP PROCEDURE IF EXISTS loopBuildings $$
    
DELIMITER ;

SET foreign_key_checks = TRUE;
```