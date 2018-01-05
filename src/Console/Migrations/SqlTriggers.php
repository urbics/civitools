<?php
namespace Urbics\Civitools\Console\Migrations;

class SqlTriggers
{
    /**
     * Generate a schema for triggers
     *
     * @return array
     */
    public function buildSchema()
    {
        $schema = [
          'civicrm_activity' => [
            'before' => [
              'insert' => [
                'set' => 'NEW.created_date = CURRENT_TIMESTAMP',
              ],
              'update' => [
                'update' => 'civicrm_case',
                'set' => 'modified_date = CURRENT_TIMESTAMP',
                'where' => 'id IN (SELECT ca.case_id FROM civicrm_case_activity ca WHERE ca.activity_id = OLD.id)',
              ],
              'delete' => [
                'update' => 'civicrm_case',
                'set' => 'modified_date = CURRENT_TIMESTAMP',
                'where' => 'id IN (SELECT ca.case_id FROM civicrm_case_activity ca WHERE ca.activity_id = OLD.id)',
              ],
            ],
          ],
          'civicrm_address' => [
            'after' => [
              'insert' => [
                'update' => 'civicrm_contact',
                'set' => 'modified_date = CURRENT_TIMESTAMP',
                'where' => 'id = NEW.contact_id',
              ],
              'update' => [
                'update' => 'civicrm_contact',
                'set' => 'modified_date = CURRENT_TIMESTAMP',
                'where' => 'id = NEW.contact_id',
              ],
              'delete' => [
                'update' => 'civicrm_contact',
                'set' => 'modified_date = CURRENT_TIMESTAMP',
                'where' => 'id = OLD.contact_id',
              ],
            ],
          ],
          'civicrm_case' => [
            'before' => [
              'insert' => [
                'set' => 'NEW.created_date = CURRENT_TIMESTAMP',
              ],
            ],
          ],
          'civicrm_case_activity' => [
            'after' => [
              'insert' => [
                'update' => 'civicrm_case',
                'set' => 'modified_date = CURRENT_TIMESTAMP',
                'where' => 'id = NEW.case_id',
              ],
            ],
          ],
          'civicrm_contact' => [
            'before' => [
              'insert' => [
                'set' => 'NEW.created_date = CURRENT_TIMESTAMP',
              ],
            ],
          ],
          'civicrm_email' => [
            'after' => [
              'insert' => [
                'update' => 'civicrm_contact',
                'set' => 'modified_date = CURRENT_TIMESTAMP',
                'where' => 'id = NEW.contact_id',
              ],
              'update' => [
                'update' => 'civicrm_contact',
                'set' => 'modified_date = CURRENT_TIMESTAMP',
                'where' => 'id = NEW.contact_id',
              ],
              'delete' => [
                'update' => 'civicrm_contact',
                'set' => 'modified_date = CURRENT_TIMESTAMP',
                'where' => 'id = OLD.contact_id',
              ],
            ],
          ],
          'civicrm_im' => [
            'after' => [
              'insert' => [
                'update' => 'civicrm_contact',
                'set' => 'modified_date = CURRENT_TIMESTAMP',
                'where' => 'id = NEW.contact_id',
              ],
              'update' => [
                'update' => 'civicrm_contact',
                'set' => 'modified_date = CURRENT_TIMESTAMP',
                'where' => 'id = NEW.contact_id',
              ],
              'delete' => [
                'update' => 'civicrm_contact',
                'set' => 'modified_date = CURRENT_TIMESTAMP',
                'where' => 'id = OLD.contact_id',
              ],
            ],
          ],
          'civicrm_mailing' => [
            'before' => [
              'insert' => [
                'set' => 'NEW.created_date = CURRENT_TIMESTAMP',
              ],
            ],
          ],
          'civicrm_phone' => [
            'before' => [
              'insert' => [
                'set' => 'NEW.phone_numeric = civicrm_strip_non_numeric(NEW.phone)',
              ],
              'update' => [
                'set' => 'NEW.phone_numeric = civicrm_strip_non_numeric(NEW.phone)',
              ],
            ],
            'after' => [
              'insert' => [
                'update' => 'civicrm_contact',
                'set' => 'modified_date = CURRENT_TIMESTAMP',
                'where' => 'id = NEW.contact_id',
              ],
              'update' => [
                'update' => 'civicrm_contact',
                'set' => 'modified_date = CURRENT_TIMESTAMP',
                'where' => 'id = NEW.contact_id',
              ],
              'delete' => [
                'update' => 'civicrm_contact',
                'set' => 'modified_date = CURRENT_TIMESTAMP',
                'where' => 'id = OLD.contact_id',
              ],
            ],
          ],
          'civicrm_website' => [
            'after' => [
              'insert' => [
                'update' => 'civicrm_contact',
                'set' => 'modified_date = CURRENT_TIMESTAMP',
                'where' => 'id = NEW.contact_id',
              ],
              'update' => [
                'update' => 'civicrm_contact',
                'set' => 'modified_date = CURRENT_TIMESTAMP',
                'where' => 'id = NEW.contact_id',
              ],
              'delete' => [
                'update' => 'civicrm_contact',
                'set' => 'modified_date = CURRENT_TIMESTAMP',
                'where' => 'id = OLD.contact_id',
              ],
            ],
          ],
        ];

        $tables['create_trigger'] = [];
        foreach ($schema as $table => $item) {
            if (!array_get($tables, "create_trigger.{$table}")) {
                $tables['create_trigger'][$table] = ['name' => $table, 'fields' => []];
            }
            foreach ($item as $when => $values) {
                foreach ($values as $event => $value) {
                    $lineSpacer = "\n" . str_repeat(' ', 12);
                    $indent = str_repeat(' ', 4);
                    $triggerName = "{$table}_{$when}_{$event}" . (isset($value['update']) ? '_update_' . $value['update'] : '');
                    $upTrigger = "{$lineSpacer}"
                        . "CREATE TRIGGER {$triggerName} " . strtoupper($when) . " " . strtoupper($event)
                        . " ON {$table} FOR EACH ROW{$lineSpacer}BEGIN{$lineSpacer}{$indent}";
                    if (isset($value['update'])) {
                        $upTrigger .= "UPDATE " . $value['update'] . " SET " . $value['set'];
                        $upTrigger .= (isset($value['where']) ? " WHERE " . $value['where'] : '') . ';';
                    } else {
                        $upTrigger .= "SET " . $value['set'] . ';';
                    }
                    $upTrigger .= "{$lineSpacer}END{$lineSpacer}";
                    $downTrigger = "DROP TRIGGER IF EXISTS {$triggerName};";
                    $tables['create_trigger'][$table]['fields'][$triggerName] = [
                      'name' => $triggerName,
                      'when' => $when,
                      'event' => $event,
                      'sql_up' => $upTrigger,
                      'sql_down' => $downTrigger,
                    ];
                }
            }
        }

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
   * Get a list of triggers for the contact table.
   *
   * @see hook_civicrm_triggerInfo
   * @see CRM_Core_DAO::triggerRebuild
   * @see http://issues.civicrm.org/jira/browse/CRM-10554
   *
   * @param $info
   * @param null $tableName
  public static function triggerInfo(&$info, $tableName = NULL) {
    //during upgrade, first check for valid version and then create triggers
    //i.e the columns created_date and modified_date are introduced in 4.3.alpha1 so dont create triggers for older version
    if (CRM_Core_Config::isUpgradeMode()) {
      $currentVer = CRM_Core_BAO_Domain::version(TRUE);
      //if current version is less than 4.3.alpha1 dont create below triggers
      if (version_compare($currentVer, '4.3.alpha1') < 0) {
        return;
      }
    }

    // Modifications to these records should update the contact timestamps.
    \Civi\Core\SqlTrigger\TimestampTriggers::create('civicrm_contact', 'Contact')
      ->setRelations(array(
          array('table' => 'civicrm_address', 'column' => 'contact_id'),
          array('table' => 'civicrm_email', 'column' => 'contact_id'),
          array('table' => 'civicrm_im', 'column' => 'contact_id'),
          array('table' => 'civicrm_phone', 'column' => 'contact_id'),
          array('table' => 'civicrm_website', 'column' => 'contact_id'),
        )
      )
      ->alterTriggerInfo($info, $tableName);

    // Update phone table to populate phone_numeric field
    if (!$tableName || $tableName == 'civicrm_phone') {
      // Define stored sql function needed for phones
      $sqlTriggers = Civi::service('sql_triggers');
      $sqlTriggers->enqueueQuery(self::DROP_STRIP_FUNCTION_43);
      $sqlTriggers->enqueueQuery(self::CREATE_STRIP_FUNCTION_43);
      $info[] = array(
        'table' => array('civicrm_phone'),
        'when' => 'BEFORE',
        'event' => array('INSERT', 'UPDATE'),
        'sql' => "\nSET NEW.phone_numeric = civicrm_strip_non_numeric(NEW.phone);\n",
      );
    }
  }

   * Add our list of triggers to the global list.
   *
   * @see \CRM_Utils_Hook::triggerInfo
   * @see \CRM_Core_DAO::triggerRebuild
   *
   * @param array $info
   *   See hook_civicrm_triggerInfo.
   * @param string|NULL $tableFilter
   *   See hook_civicrm_triggerInfo.
  public function alterTriggerInfo(&$info, $tableFilter = NULL) {
    // If we haven't upgraded yet, then the created_date/modified_date may not exist.
    // In the past, this was a version-based check, but checkFieldExists()
    // seems more robust.
    if (\CRM_Core_Config::isUpgradeMode()) {
      if (!\CRM_Core_DAO::checkFieldExists($this->getTableName(),
        $this->getCreatedDate())
      ) {
        return;
      }
    }

    if ($tableFilter == NULL || $tableFilter == $this->getTableName()) {
      $info[] = array(
        'table' => array($this->getTableName()),
        'when' => 'BEFORE',
        'event' => array('INSERT'),
        'sql' => "\nSET NEW.{$this->getCreatedDate()} = CURRENT_TIMESTAMP;\n",
      );
    }

    // Update timestamp when modifying closely related tables
    $relIdx = \CRM_Utils_Array::index(
      array('column', 'table'),
      $this->getAllRelations()
    );
    foreach ($relIdx as $column => $someRelations) {
      $this->generateTimestampTriggers($info, $tableFilter,
        array_keys($someRelations), $column);
    }
  }

   * Generate triggers to update the timestamp.
   *
   * The corresponding civicrm_FOO row is updated on insert/update/delete
   * to a table that extends civicrm_FOO.
   * Don't regenerate triggers for all such tables if only asked for one table.
   *
   * @param array $info
   *   Reference to the array where generated trigger information is being stored
   * @param string|null $tableFilter
   *   Name of the table for which triggers are being generated, or NULL if all tables
   * @param array $relatedTableNames
   *   Array of all core or all custom table names extending civicrm_FOO
   * @param string $contactRefColumn
   *   'contact_id' if processing core tables, 'entity_id' if processing custom tables
   *
   * @link https://issues.civicrm.org/jira/browse/CRM-15602
   * @see triggerInfo
  public function generateTimestampTriggers(
    &$info,
    $tableFilter,
    $relatedTableNames,
    $contactRefColumn
  ) {
    // Safety
    $contactRefColumn = \CRM_Core_DAO::escapeString($contactRefColumn);

    // If specific related table requested, just process that one.
    // (Reply: This feels fishy.)
    if (in_array($tableFilter, $relatedTableNames)) {
      $relatedTableNames = array($tableFilter);
    }

    // If no specific table requested (include all related tables),
    // or a specific related table requested (as matched above)
    if (empty($tableFilter) || isset($relatedTableNames[$tableFilter])) {
      $info[] = array(
        'table' => $relatedTableNames,
        'when' => 'AFTER',
        'event' => array('INSERT', 'UPDATE'),
        'sql' => "\nUPDATE {$this->getTableName()} SET {$this->getModifiedDate()} = CURRENT_TIMESTAMP WHERE id = NEW.$contactRefColumn;\n",
      );
      $info[] = array(
        'table' => $relatedTableNames,
        'when' => 'AFTER',
        'event' => array('DELETE'),
        'sql' => "\nUPDATE {$this->getTableName()} SET {$this->getModifiedDate()} = CURRENT_TIMESTAMP WHERE id = OLD.$contactRefColumn;\n",
      );
    }
  }  
 */    
}