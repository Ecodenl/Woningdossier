## Procedure in SQL

During the Upgrade Do we had to correct some data. We used an
SQL procedure to do some of it. This file is purely to keep history
of this procedure, since it is actually very useful, but a little
difficult to understand. Keeping this here can perhaps provide an 
answer to a problem in the future, without having to rethink 
the whole structure.

```sql
-- Disable foreign key checks
SET foreign_key_checks = FALSE;

-- Update all building IDs
UPDATE buildings SET id = id + 78000 WHERE user_id >= 90000 AND id < 90000;

-- Time to do magic in SQL; We need to update each table with each new building ID!

-- Set Delimiter to not make SQL freak out
DELIMITER $$

-- Drop existing procedures
DROP PROCEDURE IF EXISTS fixBuildings $$
DROP PROCEDURE IF EXISTS loopBuildings $$

-- Create a procedure; This procedure will update all tables with a building_id column to the given ID, where the old
-- ID is the given ID - 78000 (the increment passed in above UPDATE statement)
CREATE PROCEDURE fixBuildings(buildingId INT)
BEGIN
    -- Ensure to use variable names that do not match column / table names!!

    -- Declare a variable so we can end the loop when the cursor is finished
	DECLARE done INT DEFAULT FALSE; 
	DECLARE tableName VARCHAR(64);
	
    -- Define the cursor from a query
	DECLARE tableCursor CURSOR FOR
        SELECT DISTINCT TABLE_NAME
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE COLUMN_NAME = 'building_id';
    
    -- Ensure the variable gets updated when the cursor cannot find more rows
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    -- Enable the cursor
    OPEN tableCursor;
    
    -- Define a loop
    read_loop: LOOP
        -- Fetch the next cursor result
        FETCH tableCursor INTO tableName;
        -- Leave loop if no more rows
		IF done THEN
			LEAVE read_loop;
        END IF;
        
        -- Prepare and execute an update statement
        SET @qry = CONCAT('UPDATE ', tableName, ' SET building_id = ', buildingId, ' WHERE BUILDING_ID = ', buildingId, ' - 78000');
        PREPARE qry FROM @qry;
        EXECUTE qry;
    END LOOP;
    -- Close the cursor
    CLOSE tableCursor;
END$$

-- This procedure will loop all buildings that require having their building_id updated on related tables
CREATE PROCEDURE loopBuildings()
BEGIN 
    DECLARE done INT DEFAULT FALSE; 
	DECLARE buildingId INT;

    DECLARE buildingCursor CURSOR FOR
        SELECT id FROM buildings WHERE user_id >= 90000;

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

-- Cleanup
DROP PROCEDURE IF EXISTS fixBuildings $$
DROP PROCEDURE IF EXISTS loopBuildings $$
    
-- Reset delimiter
DELIMITER ;

-- Re-enable foreign key checks
SET foreign_key_checks = TRUE;
```