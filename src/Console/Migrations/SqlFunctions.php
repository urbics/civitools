<?php
namespace Urbics\Civitools\Console\Migrations;

class SqlFunctions
{
    /**
     * Generate a schema for functions
     *
     * @return array
     */
    public function buildSchema()
    {
        $tables['create_function'] = [
            'civicrm_contact' => [
                'name' => 'civicrm_contact',
                'fields' => [
                  'civicrm_strip_non_numeric' => [
                    'name' => 'civicrm_strip_non_numeric',
                    'sql' => "
                    DELIMITER=;;
                    CREATE FUNCTION civicrm_strip_non_numeric(input VARCHAR(255) CHARACTER SET utf8)
                      RETURNS VARCHAR(255) CHARACTER SET utf8
                      DETERMINISTIC
                      NO SQL
                    BEGIN
                      DECLARE output   VARCHAR(255) CHARACTER SET utf8 DEFAULT '';
                      DECLARE iterator INT DEFAULT 1;
                      WHILE iterator < (LENGTH(input) + 1) DO
                        IF SUBSTRING(input, iterator, 1) IN ('0', '1', '2', '3', '4', '5', '6', '7', '8', '9') THEN
                          SET output = CONCAT(output, SUBSTRING(input, iterator, 1));
                        END IF;
                        SET iterator = iterator + 1;
                      END WHILE;
                      RETURN output;
                    END;;
                    DELIMITER=;",
                  ]
                ]
            ]
        ];
          
      return $tables;
  }

    /**
     * Build the sql.
     *
     * @param  array $table
     * @return string
     */
    public function buildSql($table)
    {
        $sql = '';
        
        return $sql;
    }

/*
  const DROP_STRIP_FUNCTION_43 = "DROP FUNCTION IF EXISTS civicrm_strip_non_numeric";
  const CREATE_STRIP_FUNCTION_43 = "
    CREATE FUNCTION civicrm_strip_non_numeric(input VARCHAR(255) CHARACTER SET utf8)
      RETURNS VARCHAR(255) CHARACTER SET utf8
      DETERMINISTIC
      NO SQL
    BEGIN
      DECLARE output   VARCHAR(255) CHARACTER SET utf8 DEFAULT '';
      DECLARE iterator INT          DEFAULT 1;
      WHILE iterator < (LENGTH(input) + 1) DO
        IF SUBSTRING(input, iterator, 1) IN ('0', '1', '2', '3', '4', '5', '6', '7', '8', '9') THEN
          SET output = CONCAT(output, SUBSTRING(input, iterator, 1));
        END IF;
        SET iterator = iterator + 1;
      END WHILE;
      RETURN output;
    END";
 */    

}