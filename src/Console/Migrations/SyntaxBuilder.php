<?php
namespace Urbics\Civitools\Console\Migrations;

use Urbics\Civitools\Migrations\GeneratorException;

class SyntaxBuilder
{
    /**
     * A template to be inserted.
     *
     * @var string
     */
    private $template;

    /**
     * Create the PHP syntax for the given schema.
     *
     * @param  array $schema
     * @param  array $meta
     * @return string
     */
    public function create($schema, $meta)
    {
        $up = $this->createSchemaForUpMethod($schema, $meta);
        $down = $this->createSchemaForDownMethod($schema, $meta);

        return compact('up', 'down');
    }

    /**
     * Create the schema for the "up" method.
     *
     * @param  string $schema
     * @param  array $meta
     * @return string
     * @throws GeneratorException
     */
    private function createSchemaForUpMethod($schema, $meta)
    {
        $action = explode('_', $meta['action']);
        $schemaType = (empty($action[1]) ? 'Column' : title_case($action[1]));

        $fields = $this->constructSchema($schema, 'Add', $schemaType);
        if ($meta['action'] == 'create') {
            return $this->insert($fields)->into($this->getCreateSchemaWrapper());
        }

        if ($meta['action'] == 'create_function') {
            return $this->insert($fields)->into($this->getCreateFunctionSchemaWrapper());
        }

        if ($meta['action'] == 'create_trigger') {
            return $this->insert($fields)->into($this->getCreateTriggerSchemaWrapper());
        }

        if ($meta['action'] == 'update') {
            return $this->insert($fields)->into($this->getChangeSchemaWrapper());
        }

        if ($meta['action'] == 'remove') {
            $fields = $this->constructSchema($schema, 'Drop');

            return $this->insert($fields)->into($this->getChangeSchemaWrapper());
        }

        // Otherwise, we have no idea how to proceed.
        throw new GeneratorException;
    }

    /**
     * Construct the syntax for a down field.
     *
     * @param  array $schema
     * @param  array $meta
     * @return string
     * @throws GeneratorException
     */
    private function createSchemaForDownMethod($schema, $meta)
    {
        // If the user created a table, then for the down
        // method, we should drop it.
        if ($meta['action'] == 'create') {
            return sprintf("Schema::drop('%s');", $schema['name']);
        }

        // If the user created a function, then for the down
        // method, we should drop it.
        if ($meta['action'] == 'create_function') {
            $fields = $this->constructSchema($schema, 'Drop', 'Function');
            return $this->insert($fields)->into($this->getDropFunctionSchemaWrapper(), 'schema_down');
        }

        // If the user created a trigger, then for the down
        // method, we should drop it.
        if ($meta['action'] == 'create_trigger') {
            $fields = $this->constructSchema($schema, 'Drop', 'Trigger');
            return $this->insert($fields)->into($this->getDropTriggerSchemaWrapper(), 'schema_down');
        }

        // If the user added columns to a table, then for
        // the down method, we should remove them.
        if ($meta['action'] == 'update') {
            $fields = $this->constructSchema($schema, 'Drop');

            return $this->insert($fields)->into($this->getChangeSchemaWrapper());
        }

        // If the user removed columns from a table, then for
        // the down method, we should add them back on.
        if ($meta['action'] == 'remove') {
            $fields = $this->constructSchema($schema);

            return $this->insert($fields)->into($this->getChangeSchemaWrapper());
        }

        // Otherwise, we have no idea how to proceed.
        throw new GeneratorException;
    }

    /**
     * Store the given template, to be inserted somewhere.
     *
     * @param  string $template
     * @return $this
     */
    private function insert($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get the stored template, and insert into the given wrapper.
     *
     * @param  string $wrapper
     * @param  string $placeholder
     * @return mixed
     */
    private function into($wrapper, $placeholder = 'schema_up')
    {
        return str_replace('{{' . $placeholder . '}}', $this->template, $wrapper);
    }

    /**
     * Get the wrapper template for a "create" action.
     *
     * @return string
     */
    private function getCreateSchemaWrapper()
    {
        return file_get_contents(dirname(__DIR__) . '/Migrations/Stubs/schema-create.stub');
    }

    /**
     * Get the wrapper template for a "create_function" action.
     *
     * @return string
     */
    private function getCreateFunctionSchemaWrapper()
    {
        return file_get_contents(dirname(__DIR__) . '/Migrations/Stubs/schema-create-function.stub');
    }

    /**
     * Get the wrapper template for a "create_function" drop action.
     *
     * @return string
     */
    private function getDropFunctionSchemaWrapper()
    {
        return file_get_contents(dirname(__DIR__) . '/Migrations/Stubs/schema-drop-function.stub');
    }

    /**
     * Get the wrapper template for a "create_trigger" action.
     *
     * @return string
     */
    private function getCreateTriggerSchemaWrapper()
    {
        return file_get_contents(dirname(__DIR__) . '/Migrations/Stubs/schema-create-trigger.stub');
    }

    /**
     * Get the wrapper template for a "drop_trigger" action.
     *
     * @return string
     */
    private function getDropTriggerSchemaWrapper()
    {
        return file_get_contents(dirname(__DIR__) . '/Migrations/Stubs/schema-drop-trigger.stub');
    }

    /**
     * Get the wrapper template for an "add" action.
     *
     * @return string
     */
    private function getChangeSchemaWrapper()
    {
        return file_get_contents(dirname(__DIR__) . '/Migrations/Stubs/schema-change.stub');
    }

    /**
     * Construct the schema fields.
     *
     * @param  array $schema
     * @param  string $direction
     * @return array
     */
    private function constructSchema($schema, $direction = 'Add', $schemaType = 'Column')
    {
        if (!$schema) {
            return '';
        }
        $fields = array_map(function ($field) use ($direction, $schemaType) {
            $method = camel_case("{$direction}{$schemaType}");

            return $this->$method($field);
        }, $schema['fields']);

        return implode("\n" . str_repeat(' ', 12), $fields);
    }


    /**
     * Construct the syntax to add a column.
     *
     * @param  string $field
     * @return string
     */
    private function addColumn($field)
    {
        if (in_array($field['type'], ['index', 'unique', 'primary', 'foreign'])) {
            // Indexes have the optional index name as the second argument
            $syntax = sprintf("\$table->%s(%s, '%s')", $field['type'], $field['arguments'], $field['name']);
            if (!empty($field['options'])) {
                foreach ($field['options'] as $method => $value) {
                    $syntax .= sprintf("->%s(%s)", $method, $value === true ? '' : $value);
                }
            }
            return $syntax .= ';';
        }

        $syntax = sprintf("\$table->%s('%s')", $field['type'], $field['name']);

        // If there are arguments for the schema type, like decimal('amount', 5, 2)
        // then we have to remember to work those in.
        if (!empty($field['arguments'])) {
            $syntax = substr($syntax, 0, -1) . ', ';
            $syntax .= $field['arguments'] . ')';
        }

        if (!empty($field['options'])) {
            foreach ($field['options'] as $method => $value) {
                $syntax .= sprintf("->%s(%s)", $method, $value === true ? '' : $value);
            }
        }

        return $syntax .= ';';
    }

    /**
     * Construct the syntax to add a function.
     *
     * @param  string $field
     * @return string
     */
    private function addFunction($field)
    {
        return "DB::raw('" . $field['sql_up'] . "');\n";
    }

    /**
     * Construct the syntax to drop a function.
     *
     * @param  string $field
     * @return string
     */
    private function dropFunction($field)
    {
        return "DB::raw('" . $field['sql_down'] . "');";
    }

    /**
     * Construct the syntax to add a trigger.
     *
     * @param  string $field
     * @return string
     */
    private function addTrigger($field)
    {
        return "DB::raw('" . $field['sql_up'] . "');\n";
    }

    /**
     * Construct the syntax to drop a trigger.
     *
     * @param  string $field
     * @return string
     */
    private function dropTrigger($field)
    {
        return "DB::raw('" . $field['sql_down'] . "');";
    }

    /**
     * Construct the syntax to drop a column.
     *
     * @param  string $field
     * @return string
     */
    private function dropColumn($field)
    {
        if (in_array($field['type'], ['index', 'unique', 'primary', 'foreign'])) {
            return sprintf("\$table->drop%s('%s');", title_case($field['type']), $field['name']);
        }
        return sprintf("\$table->dropColumn('%s');", $field['name']);
    }
}
