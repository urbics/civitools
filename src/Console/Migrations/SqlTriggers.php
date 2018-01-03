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
        $items = [];
        
        return $items;
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